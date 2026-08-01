"""
Coupling Analyzer

Menghitung Efferent Coupling (Ce)
berdasarkan dependency yang digunakan
oleh Laravel Controller.
"""

import re


class CouplingAnalyzer:

    def analyze(self, source_code: str):

        dependencies = set()

        # ==========================================
        # use statement
        # ==========================================

        use_pattern = r"^use\s+(.+?);"

        for match in re.findall(
            use_pattern,
            source_code,
            re.MULTILINE
        ):
            dependencies.add(match.strip())

        # ==========================================
        # Facades
        # ==========================================

        facades = [

            "DB::",

            "Storage::",

            "Mail::",

            "Cache::",

            "Auth::",

            "Hash::",

            "Log::",

            "Http::"

        ]

        for facade in facades:

            if facade in source_code:

                dependencies.add(facade)

        # ==========================================
        # Result
        # ==========================================

        return {

            "ce": len(dependencies),

            "dependencies": sorted(list(dependencies))

        }