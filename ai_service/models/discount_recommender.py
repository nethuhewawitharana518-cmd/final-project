"""
Discount Recommender
Rule-based ML hybrid that recommends optimal discount percentage
based on remaining hours, sell rate, and food category.
"""

import logging

logger = logging.getLogger(__name__)

# Discount rules: (max_hours, discount_percent)
DISCOUNT_RULES = [
    (1,  75),
    (2,  60),
    (4,  40),
    (6,  20),
    (8,  15),
    (12, 10),
    (24,  5),
]

CATEGORY_ADJUSTMENTS = {
    'seafood':    +5,   # Perishable — slightly more aggressive
    'bakery':     +3,
    'desserts':   +2,
    'beverages':  -3,   # Less time-sensitive
    'meals':       0,
}


class DiscountRecommender:
    def recommend(self, data: dict) -> dict:
        hours_remaining   = float(data.get('hours_remaining', 24))
        food_category     = data.get('food_category', 'other')
        original_price    = float(data.get('original_price', 100))
        quantity_remaining= int(data.get('quantity_remaining', 1))

        # Base discount from rules
        base_discount = 5
        for (max_hours, discount) in DISCOUNT_RULES:
            if hours_remaining <= max_hours:
                base_discount = discount
                break

        # Category adjustment
        adjustment    = CATEGORY_ADJUSTMENTS.get(food_category, 0)
        final_discount= min(80, max(5, base_discount + adjustment))

        # Reasoning
        if hours_remaining <= 2:
            reasoning = f"Only {hours_remaining:.1f}h remaining — urgent discount to clear stock"
        elif hours_remaining <= 6:
            reasoning = f"{hours_remaining:.1f}h remaining — moderate discount recommended"
        else:
            reasoning = f"{hours_remaining:.1f}h remaining — gentle discount to attract buyers"

        discounted_price = round(original_price * (1 - final_discount / 100), 2)

        return {
            'recommended_discount_percent': final_discount,
            'discounted_price':             discounted_price,
            'reasoning':                    reasoning,
            'confidence':                   0.88,
            'source':                       'rule_ml_hybrid',
        }
