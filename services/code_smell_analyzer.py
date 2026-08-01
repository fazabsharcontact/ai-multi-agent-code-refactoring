"""
Code Smell Analyzer

Menganalisis Code Smell berdasarkan
Martin Fowler.

Kategori:
1. Long Method
2. Large Class
3. Duplicated Code
"""

import re


class CodeSmellAnalyzer:

    def analyze(self, source_code: str):

        result = {
            "long_method": [],
            "large_class": [],
            "duplicated_code": []
        }

        # =====================================================
        # LONG METHOD
        # =====================================================

        methods = re.findall(
            r'public\s+function\s+\w+.*?\{([\s\S]*?)^\}',
            source_code,
            re.MULTILINE
        )

        for index, method in enumerate(methods, start=1):

            lines = len([
                line for line in method.splitlines()
                if line.strip()
            ])

            if lines > 30:

                result["long_method"].append({

                    "rule": "Long Method",

                    "severity": "High",

                    "description":
                        f"Method ke-{index} memiliki {lines} baris."

                })

        # =====================================================
        # LARGE CLASS
        # =====================================================

        total_lines = len([
            line
            for line in source_code.splitlines()
            if line.strip()
        ])

        total_methods = len(re.findall(

            r'public\s+function',

            source_code

        ))

        if total_lines > 200 or total_methods > 10:

            result["large_class"].append({

                "rule": "Large Class",

                "severity": "High",

                "description":
                    f"Controller memiliki {total_lines} LOC dan {total_methods} method."

            })

        # =====================================================
        # DUPLICATED CODE
        # =====================================================

        lines = [

            line.strip()

            for line in source_code.splitlines()

            if line.strip()

        ]

        duplicate = {}

        for line in lines:

            duplicate[line] = duplicate.get(line, 0) + 1

        duplicated_lines = [

            line

            for line, total in duplicate.items()

            if total >= 3

        ]

        if duplicated_lines:

            result["duplicated_code"].append({

                "rule": "Duplicated Code",

                "severity": "Medium",

                "description":
                    f"Ditemukan {len(duplicated_lines)} baris yang berulang."

            })

        return result