"""
Maintainability Analyzer

Menghitung:
- Lines of Code (LOC)
- Cyclomatic Complexity (CC)
- Halstead Volume
- Maintainability Index (MI)
"""

from radon.raw import analyze
from radon.complexity import cc_visit
from radon.metrics import h_visit, mi_visit


class MaintainabilityAnalyzer:

    def analyze(self, source_code: str):
        """
        Mengembalikan metrik maintainability
        """

        # ======================
        # Lines of Code
        # ======================

        raw = analyze(source_code)

        loc = raw.loc

        # ======================
        # Cyclomatic Complexity
        # ======================

        complexity_result = cc_visit(source_code)

        if complexity_result:
            cc = max(item.complexity for item in complexity_result)
        else:
            cc = 1

        # ======================
        # Halstead Volume
        # ======================

        try:
            halstead = h_visit(source_code)

            volume = halstead.total.volume

        except Exception:
            volume = 0

        # ======================
        # Maintainability Index
        # ======================

        try:
            mi = mi_visit(source_code, multi=True)

        except Exception:
            mi = 0

        # ======================
        # Category
        # ======================

        if mi >= 85:
            category = "High Maintainability"

        elif mi >= 65:
            category = "Medium Maintainability"

        else:
            category = "Low Maintainability"

        return {

            "loc": loc,

            "cyclomatic_complexity": cc,

            "halstead_volume": round(volume, 2),

            "maintainability_index": round(mi, 2),

            "category": category

        }