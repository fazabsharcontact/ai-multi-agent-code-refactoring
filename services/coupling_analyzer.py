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

        # =====================================================
        # use statements
        # =====================================================

        for match in re.findall(

            r"^\s*use\s+([^;]+);",

            source_code,

            re.MULTILINE

        ):

            dependencies.add(match.strip())

        # =====================================================
        # Constructor Injection
        # =====================================================

        constructor_pattern = (

            r"__construct\s*\((.*?)\)"

        )

        constructor = re.search(

            constructor_pattern,

            source_code,

            re.DOTALL

        )

        if constructor:

            params = constructor.group(1)

            for match in re.findall(

                r"([A-Z][A-Za-z0-9_]+)\s+\$",

                params

            ):

                dependencies.add(match)

        # =====================================================
        # new ClassName()
        # =====================================================

        for match in re.findall(

            r"new\s+([A-Z][A-Za-z0-9_\\\\]+)",

            source_code

        ):

            dependencies.add(match)

        # =====================================================
        # app(Class::class)
        # =====================================================

        for match in re.findall(

            r"app\(\s*([A-Z][A-Za-z0-9_\\\\]+)::class",

            source_code

        ):

            dependencies.add(match)

        # =====================================================
        # resolve(Class::class)
        # =====================================================

        for match in re.findall(

            r"resolve\(\s*([A-Z][A-Za-z0-9_\\\\]+)::class",

            source_code

        ):

            dependencies.add(match)

        # =====================================================
        # Laravel Facades
        # =====================================================

        facades = [

            "DB",

            "Storage",

            "Mail",

            "Cache",

            "Auth",

            "Hash",

            "Log",

            "Http",

            "Validator",

            "File",

            "Gate",

            "Queue",

            "Notification"

        ]

        for facade in facades:

            if f"{facade}::" in source_code:

                dependencies.add(facade)

        # =====================================================
        # Return
        # =====================================================

        return {

            "ce": len(dependencies),

            "dependencies": sorted(dependencies)

        }