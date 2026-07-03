"""
Demand Forecaster
Predicts peak demand hours and weekly sales trends for a business
using linear regression on simulated historical sales patterns.
"""

import numpy as np
import logging
from sklearn.linear_model import LinearRegression

logger = logging.getLogger(__name__)

# Simulated peak hour patterns (0-23)
PEAK_PATTERNS = {
    'restaurant': [7, 8, 12, 13, 19, 20],
    'bakery':     [7, 8, 9, 15, 16],
    'cafe':       [8, 9, 10, 15, 16, 17],
    'hotel':      [7, 8, 12, 18, 19, 20],
    'supermarket':[10, 11, 17, 18, 19],
    'default':    [12, 13, 18, 19],
}


class DemandForecaster:
    def forecast(self, business_id: int) -> dict:
        """
        Generate a 7-day demand forecast for a business.
        In production, replace simulated data with real DB queries.
        """
        np.random.seed(business_id % 1000)

        # Simulate historical weekly data (last 4 weeks)
        days   = np.arange(28).reshape(-1, 1)
        sales  = 50 + 20 * np.sin(days.flatten() * 0.8) + np.random.normal(0, 5, 28)
        sales  = np.maximum(sales, 0)

        # Fit linear regression
        model = LinearRegression()
        model.fit(days, sales)

        # Predict next 7 days
        future_days  = np.arange(28, 35).reshape(-1, 1)
        predictions  = model.predict(future_days)
        predictions  = np.maximum(predictions, 0).round(1).tolist()

        day_names = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']

        forecast = [
            {'day': day_names[i % 7], 'predicted_orders': pred, 'confidence': 0.78}
            for i, pred in enumerate(predictions)
        ]

        # Peak hours (simulated)
        peak_hours = PEAK_PATTERNS.get('default', [12, 18])

        # Category distribution (simulated)
        categories    = ['Meals', 'Rice & Curry', 'Bakery', 'Beverages', 'Other']
        distributions = np.random.dirichlet(np.ones(5) * 3).round(3).tolist()

        return {
            'business_id':       business_id,
            'forecast':          forecast,
            'peak_hours':        peak_hours,
            'category_demand':   [
                {'category': cat, 'share': share}
                for cat, share in zip(categories, distributions)
            ],
            'trend':             'rising' if model.coef_[0] > 0 else 'falling',
            'model':             'LinearRegression',
        }
