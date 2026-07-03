"""
AI Microservice for Food Rescue Marketplace
Flask REST API serving expiry risk prediction,
discount recommendations, and demand forecasting.
"""

from flask import Flask, request, jsonify
import joblib
import os
import logging
from models.expiry_predictor import ExpiryPredictor
from models.discount_recommender import DiscountRecommender
from models.demand_forecaster import DemandForecaster

# ─── Setup ───────────────────────────────────────────────────
logging.basicConfig(level=logging.INFO, format='%(asctime)s %(levelname)s %(message)s')
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Load or initialize models
expiry_predictor   = ExpiryPredictor()
discount_recommender = DiscountRecommender()
demand_forecaster  = DemandForecaster()

# ─── Health Check ─────────────────────────────────────────────
@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'ok', 'service': 'Food Rescue AI Microservice'})


# ─── Expiry Risk Prediction ───────────────────────────────────
@app.route('/ai/expiry-risk', methods=['POST'])
def expiry_risk():
    """
    Predict food expiry risk level.
    Input:  { food_category, hours_remaining, original_quantity,
              qty_remaining, time_of_day, day_of_week }
    Output: { risk: low|medium|high, confidence: float }
    """
    data = request.get_json()
    if not data:
        return jsonify({'error': 'No JSON body provided'}), 400

    required = ['hours_remaining']
    for field in required:
        if field not in data:
            return jsonify({'error': f'Missing field: {field}'}), 400

    try:
        result = expiry_predictor.predict(data)
        logger.info(f"Expiry risk: {data.get('food_category')} -> {result['risk']}")
        return jsonify(result)
    except Exception as e:
        logger.error(f"Expiry prediction error: {e}")
        return jsonify({'error': str(e)}), 500


# ─── Discount Recommendation ──────────────────────────────────
@app.route('/ai/discount-recommend', methods=['POST'])
def discount_recommend():
    """
    Recommend an optimal discount percentage.
    Input:  { food_category, hours_remaining, original_price, quantity_remaining }
    Output: { recommended_discount_percent: int, reasoning: str, confidence: float }
    """
    data = request.get_json()
    if not data:
        return jsonify({'error': 'No JSON body provided'}), 400

    try:
        result = discount_recommender.recommend(data)
        return jsonify(result)
    except Exception as e:
        logger.error(f"Discount recommendation error: {e}")
        return jsonify({'error': str(e)}), 500


# ─── Demand Forecasting ───────────────────────────────────────
@app.route('/ai/demand-forecast', methods=['POST'])
def demand_forecast():
    """
    Forecast demand for a business over the next 7 days.
    Input:  { business_id }
    Output: { forecast: [...], peak_hours: [...] }
    """
    data = request.get_json()
    if not data or 'business_id' not in data:
        return jsonify({'error': 'Missing business_id'}), 400

    try:
        result = demand_forecaster.forecast(data['business_id'])
        return jsonify(result)
    except Exception as e:
        logger.error(f"Demand forecast error: {e}")
        return jsonify({'error': str(e)}), 500


# ─── Entry Point ──────────────────────────────────────────────
if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=False)
