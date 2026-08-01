"""
Clean Code Analyzer

Menganalisis pelanggaran prinsip Clean Code
berdasarkan Robert C. Martin.

Kategori yang dianalisis:
1. Functions
2. Objects and Data Structures
3. Error Handling
"""

import re


class CleanCodeAnalyzer:

    def analyze(self, source_code: str):

        result = {
            "functions": [],
            "objects_data_structures": [],
            "error_handling": []
        }

        # =====================================================
        # FUNCTIONS
        # =====================================================

        functions = re.findall(
            r'function\s+\w+\s*\((.*?)\)',
            source_code,
            re.DOTALL
        )

        # Terlalu banyak parameter
        for parameter in functions:

            parameter = parameter.strip()

            if parameter == "":
                continue

            total_parameter = len(parameter.split(","))

            if total_parameter > 3:

                result["functions"].append({

                    "rule": "Too Many Parameters",

                    "severity": "Medium",

                    "description":
                    f"Function memiliki {total_parameter} parameter."

                })

        # =====================================================
        # OBJECTS AND DATA STRUCTURES
        # =====================================================

        if re.search(

            r'Request::|DB::table|Model::',

            source_code

        ):

            result["objects_data_structures"].append({

                "rule": "Direct Data Access",

                "severity": "High",

                "description":
                "Controller mengakses data secara langsung tanpa abstraction."

            })

        if re.search(

            r'new\s+\w+',

            source_code

        ):

            result["objects_data_structures"].append({

                "rule": "Direct Object Instantiation",

                "severity": "Medium",

                "description":
                "Object dibuat langsung di Controller."

            })

        # =====================================================
        # ERROR HANDLING
        # =====================================================

        if re.search(

            r'catch\s*\(.*?\)\s*\{\s*\}',

            source_code,

            re.DOTALL

        ):

            result["error_handling"].append({

                "rule": "Empty Catch",

                "severity": "High",

                "description":
                "Catch block kosong."

            })

        if re.search(

            r'try\s*\{',

            source_code

        ) and not re.search(

            r'catch\s*\(',

            source_code

        ):

            result["error_handling"].append({

                "rule": "Missing Catch",

                "severity": "High",

                "description":
                "Try tanpa catch."

            })

        return result