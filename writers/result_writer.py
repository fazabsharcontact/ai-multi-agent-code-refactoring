"""
Result Writer

Bertugas menyimpan hasil refactoring
ke folder output.
"""

from pathlib import Path


class ResultWriter:

    def __init__(self, output_dir="output"):

        self.output_dir = Path(output_dir)

    # =====================================================

    def save(self, refactoring_result):

        controller = refactoring_result["controller"]

        refactored_code = refactoring_result["refactored_code"]

        controller_path = Path(controller)

        folder = self.output_dir / controller_path.parent

        folder.mkdir(parents=True, exist_ok=True)

        filename = controller_path.stem + "_refactored.php"

        output_file = folder / filename

        with open(output_file, "w", encoding="utf-8") as f:

            f.write(refactored_code)

        return str(output_file)