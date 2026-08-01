"""
Maintainability Analyzer

Menghitung:

- Physical Lines of Code (LOC)
- Cyclomatic Complexity (CC)
- Halstead Volume
- Maintainability Index (MI)

Perhitungan mengikuti metodologi penelitian.
"""

import math
import re


class MaintainabilityAnalyzer:

    def analyze(self, source_code: str):

        # =====================================================
        # Physical Lines of Code (PLOC)
        # =====================================================

        loc = self.calculate_loc(source_code)

        # =====================================================
        # Cyclomatic Complexity
        # =====================================================

        cc = self.calculate_cc(source_code)

        # =====================================================
        # Halstead Volume
        # =====================================================

        hv = self.calculate_halstead_volume(source_code)

        # =====================================================
        # Hindari log(0)
        # =====================================================

        loc = max(loc, 1)
        hv = max(hv, 1)

        # =====================================================
        # Maintainability Index
        # =====================================================

        mi = (
            171
            - (5.2 * math.log(hv))
            - (0.23 * cc)
            - (16.2 * math.log(loc))
        )

        mi = max(0, min(mi, 171))

        # =====================================================
        # Category
        # =====================================================

        if mi >= 85:
            category = "High Maintainability"

        elif mi >= 65:
            category = "Medium Maintainability"

        else:
            category = "Low Maintainability"

        # =====================================================
        # Return
        # =====================================================

        return {

            "loc": loc,

            "cyclomatic_complexity": cc,

            "halstead_volume": round(hv, 2),

            "maintainability_index": round(mi, 2),

            "category": category

        }

    # =====================================================
    # Physical LOC
    # =====================================================

    def calculate_loc(self, source_code):

        lines = source_code.splitlines()

        count = 0

        for line in lines:

            line = line.strip()

            if line == "":
                continue

            if line.startswith("//"):
                continue

            if line.startswith("#"):
                continue

            count += 1

        return count

    # =====================================================
    # Cyclomatic Complexity
    # =====================================================

    def calculate_cc(self, source_code):

        patterns = [

            r"\bif\b",

            r"\belseif\b",

            r"\bfor\b",

            r"\bforeach\b",

            r"\bwhile\b",

            r"\bcase\b",

            r"\bcatch\b",

            r"\?\s*",

            r"&&",

            r"\|\|"

        ]

        cc = 1

        for pattern in patterns:

            cc += len(re.findall(pattern, source_code))

        return cc

    # =====================================================
    # Halstead Volume
    # =====================================================

    def calculate_halstead_volume(self, source_code):

        operators = re.findall(

            r"(==|!=|<=|>=|=>|=|\+|-|\*|/|&&|\|\||!|<|>)",

            source_code

        )

        operands = re.findall(

            r"[A-Za-z_][A-Za-z0-9_]*",

            source_code

        )

        n1 = len(set(operators))

        n2 = len(set(operands))

        N1 = len(operators)

        N2 = len(operands)

        vocabulary = n1 + n2

        length = N1 + N2

        if vocabulary <= 1:

            return 0

        return length * math.log2(vocabulary)