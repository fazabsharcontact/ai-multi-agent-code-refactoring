"""
Reusability Analyzer

Menghitung nilai Reusability Index (REI)
berdasarkan penelitian menggunakan:

- LOC
- Cyclomatic Complexity
- Efferent Coupling
"""

class ReusabilityAnalyzer:

    def __init__(self):

        # nilai minimum dataset
        self.MIN_LOC = 13
        self.MAX_LOC = 209

        self.MIN_CC = 1
        self.MAX_CC = 21

        self.MIN_CE = 2
        self.MAX_CE = 11


    def normalize(self, value, minimum, maximum):

        if maximum == minimum:
            return 0

        return (value - minimum) / (maximum - minimum)


    def analyze(self, loc, cc, ce):

        # ==========================
        # Normalisasi
        # ==========================

        loc_norm = self.normalize(
            loc,
            self.MIN_LOC,
            self.MAX_LOC
        )

        cc_norm = self.normalize(
            cc,
            self.MIN_CC,
            self.MAX_CC
        )

        ce_norm = self.normalize(
            ce,
            self.MIN_CE,
            self.MAX_CE
        )

        # ==========================
        # Reusability Index
        # ==========================

        rei = 1 - (
            (
                loc_norm +
                cc_norm +
                ce_norm
            ) / 3
        )

        # ==========================
        # Kategori
        # ==========================

        if rei >= 0.75:
            category = "High Reusability"

        elif rei >= 0.50:
            category = "Medium Reusability"

        else:
            category = "Low Reusability"

        return {

            "loc_normalized": round(loc_norm, 4),

            "cc_normalized": round(cc_norm, 4),

            "ce_normalized": round(ce_norm, 4),

            "reusability_index": round(rei, 4),

            "category": category

        }