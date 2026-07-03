"""
Expiry Risk Predictor
Random Forest Classifier that predicts food expiry risk:
Low / Medium / High based on time remaining and food features.
"""

import numpy as np
import os
import joblib
import logging
from sklearn.ensemble import RandomForestClassifier
from sklearn.preprocessing import LabelEncoder

logger = logging.getLogger(__name__)

CATEGORY_MAP = {
    'meals': 0, 'rice-curry': 1, 'bakery': 2, 'desserts': 3,
    'beverages': 4, 'fast-food': 5, 'vegetarian': 6, 'seafood': 7, 'other': 8
}

MODEL_PATH = os.path.join(os.path.dirname(__file__), '..', 'trained_models', 'expiry_predictor.pkl')


class ExpiryPredictor:
    def __init__(self):
        self.model = self._load_or_train()

    def _load_or_train(self) -> RandomForestClassifier:
        if os.path.exists(MODEL_PATH):
            logger.info("Loading expiry predictor model from disk.")
            return joblib.load(MODEL_PATH)
        logger.info("Training new expiry predictor model.")
        return self._train_model()

    def _train_model(self) -> RandomForestClassifier:
        """Train on synthetic data. Replace with real data when available."""
        np.random.seed(42)
        n = 2000

        hours  = np.random.uniform(0, 24, n)
        cat    = np.random.randint(0, 9, n)
        qty    = np.random.randint(1, 50, n)
        avail  = np.array([max(0, q - np.random.randint(0, int(q)+1)) for q in qty])
        hour_d = np.random.randint(0, 24, n)
        dow    = np.random.randint(0, 7, n)

        # Labels based on business rules
        labels = np.where(hours <= 4, 2, np.where(hours <= 12, 1, 0))
        # Add some noise for realism
        noise_mask = np.random.rand(n) < 0.05
        labels[noise_mask] = np.random.randint(0, 3, noise_mask.sum())

        X = np.column_stack([hours, cat, qty, avail, hour_d, dow])
        clf = RandomForestClassifier(n_estimators=100, max_depth=8, random_state=42)
        clf.fit(X, labels)

        os.makedirs(os.path.dirname(MODEL_PATH), exist_ok=True)
        joblib.dump(clf, MODEL_PATH)
        return clf

    def predict(self, data: dict) -> dict:
        hours_remaining  = float(data.get('hours_remaining', 24))
        food_category    = CATEGORY_MAP.get(data.get('food_category', 'other'), 8)
        original_qty     = int(data.get('original_quantity', 10))
        qty_remaining    = int(data.get('qty_remaining', 5))
        time_of_day      = int(data.get('time_of_day', 12))
        day_of_week      = int(data.get('day_of_week', 0))

        X = np.array([[hours_remaining, food_category, original_qty, qty_remaining, time_of_day, day_of_week]])
        label = self.model.predict(X)[0]
        proba = self.model.predict_proba(X)[0]

        risk_map    = {0: 'low', 1: 'medium', 2: 'high'}
        confidence  = round(float(max(proba)), 4)

        return {
            'risk':       risk_map[label],
            'confidence': confidence,
            'source':     'ml_model',
        }
