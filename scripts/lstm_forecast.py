#!/usr/bin/env python3
"""LSTM forecasting bridge for Laravel.

Input : JSON via stdin.
Output: JSON via stdout only.

This implementation uses NumPy only so it also works on Windows machines where
PyTorch DLL loading fails. It is a real, trainable single-layer LSTM with an
Adam optimiser and recursive 12-month forecasting.
"""
from __future__ import annotations

import json
import math
import sys
from dataclasses import dataclass
from typing import Any

import numpy as np


@dataclass
class MinMaxScaler1D:
    minimum: float
    maximum: float

    def transform(self, values: np.ndarray) -> np.ndarray:
        span = self.maximum - self.minimum
        if span == 0:
            return np.zeros_like(values, dtype=np.float64)
        return ((values - self.minimum) / span).astype(np.float64)

    def inverse(self, values: np.ndarray) -> np.ndarray:
        span = self.maximum - self.minimum
        if span == 0:
            return np.full_like(values, self.minimum, dtype=np.float64)
        return values * span + self.minimum


def sigmoid(x: np.ndarray) -> np.ndarray:
    return 1.0 / (1.0 + np.exp(-np.clip(x, -40.0, 40.0)))


class NumpyLSTM:
    """Small many-to-one LSTM trained with back-propagation through time."""

    def __init__(self, hidden_size: int, seed: int) -> None:
        self.hidden_size = hidden_size
        rng = np.random.default_rng(seed)
        input_size = hidden_size + 1
        scale = 1.0 / math.sqrt(input_size)

        self.params = {
            "Wf": rng.normal(0.0, scale, (hidden_size, input_size)),
            "Wi": rng.normal(0.0, scale, (hidden_size, input_size)),
            "Wo": rng.normal(0.0, scale, (hidden_size, input_size)),
            "Wg": rng.normal(0.0, scale, (hidden_size, input_size)),
            "bf": np.ones((hidden_size, 1)) * 0.5,
            "bi": np.zeros((hidden_size, 1)),
            "bo": np.zeros((hidden_size, 1)),
            "bg": np.zeros((hidden_size, 1)),
            "Wy": rng.normal(0.0, scale, (1, hidden_size)),
            "by": np.zeros((1, 1)),
        }
        self.m = {name: np.zeros_like(value) for name, value in self.params.items()}
        self.v = {name: np.zeros_like(value) for name, value in self.params.items()}
        self.step = 0

    def forward(self, sequence: np.ndarray) -> tuple[float, list[dict[str, np.ndarray]]]:
        h = np.zeros((self.hidden_size, 1))
        c = np.zeros((self.hidden_size, 1))
        cache: list[dict[str, np.ndarray]] = []

        for raw_x in sequence:
            x = np.asarray([[float(raw_x)]])
            z = np.vstack((h, x))
            f = sigmoid(self.params["Wf"] @ z + self.params["bf"])
            i = sigmoid(self.params["Wi"] @ z + self.params["bi"])
            o = sigmoid(self.params["Wo"] @ z + self.params["bo"])
            g = np.tanh(self.params["Wg"] @ z + self.params["bg"])
            c_next = f * c + i * g
            h_next = o * np.tanh(c_next)
            cache.append({"z": z, "f": f, "i": i, "o": o, "g": g, "c_prev": c, "c": c_next, "h": h_next})
            h, c = h_next, c_next

        y = float((self.params["Wy"] @ h + self.params["by"])[0, 0])
        return y, cache

    def gradients(self, sequence: np.ndarray, target: float) -> tuple[float, dict[str, np.ndarray]]:
        prediction, cache = self.forward(sequence)
        error = prediction - target
        grads = {name: np.zeros_like(value) for name, value in self.params.items()}

        last_h = cache[-1]["h"]
        grads["Wy"] = error * last_h.T
        grads["by"] = np.asarray([[error]])
        dh_next = self.params["Wy"].T * error
        dc_next = np.zeros((self.hidden_size, 1))

        for state in reversed(cache):
            z, f, i, o, g = state["z"], state["f"], state["i"], state["o"], state["g"]
            c_prev, c = state["c_prev"], state["c"]
            tanh_c = np.tanh(c)

            do = dh_next * tanh_c
            dc = dc_next + dh_next * o * (1.0 - tanh_c * tanh_c)
            df = dc * c_prev
            di = dc * g
            dg = dc * i

            daf = df * f * (1.0 - f)
            dai = di * i * (1.0 - i)
            dao = do * o * (1.0 - o)
            dag = dg * (1.0 - g * g)

            grads["Wf"] += daf @ z.T
            grads["Wi"] += dai @ z.T
            grads["Wo"] += dao @ z.T
            grads["Wg"] += dag @ z.T
            grads["bf"] += daf
            grads["bi"] += dai
            grads["bo"] += dao
            grads["bg"] += dag

            dz = (
                self.params["Wf"].T @ daf
                + self.params["Wi"].T @ dai
                + self.params["Wo"].T @ dao
                + self.params["Wg"].T @ dag
            )
            dh_next = dz[: self.hidden_size]
            dc_next = dc * f

        return 0.5 * error * error, grads

    def train(self, x: np.ndarray, y: np.ndarray, epochs: int, learning_rate: float = 0.01) -> None:
        beta1, beta2, eps = 0.9, 0.999, 1e-8
        rng = np.random.default_rng(12345)

        for _ in range(epochs):
            order = rng.permutation(len(x))
            accumulated = {name: np.zeros_like(value) for name, value in self.params.items()}

            for index in order:
                _, grads = self.gradients(x[index], float(y[index]))
                for name in accumulated:
                    accumulated[name] += grads[name] / len(x)

            # Global gradient clipping prevents unstable updates on short/noisy series.
            norm = math.sqrt(sum(float(np.sum(g * g)) for g in accumulated.values()))
            if norm > 5.0:
                factor = 5.0 / (norm + 1e-12)
                accumulated = {name: grad * factor for name, grad in accumulated.items()}

            self.step += 1
            for name, grad in accumulated.items():
                self.m[name] = beta1 * self.m[name] + (1.0 - beta1) * grad
                self.v[name] = beta2 * self.v[name] + (1.0 - beta2) * (grad * grad)
                m_hat = self.m[name] / (1.0 - beta1**self.step)
                v_hat = self.v[name] / (1.0 - beta2**self.step)
                self.params[name] -= learning_rate * m_hat / (np.sqrt(v_hat) + eps)

    def predict(self, sequence: np.ndarray) -> float:
        prediction, _ = self.forward(sequence)
        return prediction


def fill_monthly_series(rows: list[dict[str, Any]]) -> tuple[list[str], np.ndarray]:
    parsed: dict[tuple[int, int], float] = {}
    for row in rows:
        period = str(row.get("period", ""))
        if len(period) != 6 or not period.isdigit():
            raise ValueError(f"Format periode tidak valid: {period!r}; gunakan YYYYMM.")
        year, month = int(period[:4]), int(period[4:])
        if month < 1 or month > 12:
            raise ValueError(f"Bulan tidak valid pada periode {period}.")
        parsed[(year, month)] = max(0.0, float(row.get("value", 0)))

    if not parsed:
        raise ValueError("Deret waktu kosong.")

    first, last = min(parsed), max(parsed)
    periods: list[str] = []
    values: list[float] = []
    year, month = first
    while (year, month) <= last:
        periods.append(f"{year:04d}{month:02d}")
        values.append(parsed.get((year, month), 0.0))
        month += 1
        if month == 13:
            month = 1
            year += 1
    return periods, np.asarray(values, dtype=np.float64)


def keep_complete_years(periods: list[str], values: np.ndarray) -> tuple[list[str], np.ndarray]:
    first_year = int(periods[0][:4])
    last_year = int(periods[-1][:4])
    if periods[-1][4:] != "12":
        last_year -= 1

    selected = [(period, float(value)) for period, value in zip(periods, values) if first_year <= int(period[:4]) <= last_year]
    if not selected:
        raise ValueError("Belum tersedia satu tahun kalender lengkap untuk prediksi.")
    return [item[0] for item in selected], np.asarray([item[1] for item in selected], dtype=np.float64)


def make_windows(values: np.ndarray, look_back: int) -> tuple[np.ndarray, np.ndarray]:
    x, y = [], []
    for index in range(look_back, len(values)):
        x.append(values[index - look_back:index])
        y.append(values[index])
    return np.asarray(x, dtype=np.float64), np.asarray(y, dtype=np.float64)


def main() -> None:
    payload = json.load(sys.stdin)
    periods, values = fill_monthly_series(payload.get("series", []))
    periods, values = keep_complete_years(periods, values)

    requested_look_back = max(3, int(payload.get("look_back", 12)))
    look_back = min(requested_look_back, max(3, len(values) // 3))
    if len(values) < look_back + 4:
        raise ValueError(
            f"Data terlalu sedikit untuk LSTM: tersedia {len(values)} bulan, "
            f"dibutuhkan minimal {look_back + 4} bulan."
        )

    seed = int(payload.get("seed", 42))
    epochs = max(50, min(int(payload.get("epochs", 250)), 1000))
    scaler = MinMaxScaler1D(float(values.min()), float(values.max()))
    scaled = scaler.transform(values)
    x, y = make_windows(scaled, look_back)

    model = NumpyLSTM(hidden_size=8, seed=seed)
    model.train(x, y, epochs=epochs, learning_rate=0.01)

    fitted_scaled = np.asarray([model.predict(window) for window in x])
    fitted = scaler.inverse(fitted_scaled)
    actual = scaler.inverse(y)
    rmse = float(math.sqrt(np.mean((actual - fitted) ** 2)))

    rolling = scaled.tolist()
    monthly_forecast: list[float] = []
    # Allow a moderate increase above the historical maximum, but block exploding recursion.
    max_monthly = max(float(values.max()) * 1.5, float(np.mean(values)) * 2.0, 1.0)
    with np.errstate(over="ignore", invalid="ignore"):
        for _ in range(12):
            window = np.asarray(rolling[-look_back:], dtype=np.float64)
            predicted_scaled = float(np.clip(model.predict(window), 0.0, 1.5))
            predicted = float(scaler.inverse(np.asarray([predicted_scaled]))[0])
            predicted = float(np.clip(predicted, 0.0, max_monthly))
            monthly_forecast.append(predicted)
            rolling.append(float(scaler.transform(np.asarray([predicted]))[0]))

    last_year = int(periods[-1][:4])
    forecast_year = last_year + 1
    forecast_total = int(round(sum(monthly_forecast)))

    output = {
        "status": "ok",
        "method": "LSTM (NumPy)",
        "forecast_year": forecast_year,
        "forecast_total": max(0, forecast_total),
        "monthly_forecast": [round(value, 2) for value in monthly_forecast],
        "rmse": round(rmse, 4),
        "look_back": look_back,
        "training_points": int(len(values)),
        "training_samples": int(len(x)),
        "last_historical_year": last_year,
    }
    json.dump(output, sys.stdout, ensure_ascii=False)


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:
        json.dump({"status": "error", "message": str(exc)}, sys.stdout, ensure_ascii=False)
        sys.exit(1)
