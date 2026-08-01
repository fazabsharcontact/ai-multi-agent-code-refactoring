"""
Result Writer

Menyimpan seluruh file hasil refactoring
ke folder output dengan struktur Laravel.
"""

from pathlib import Path


class ResultWriter:

    def __init__(self, output_dir="output"):

        self.output_dir = Path(output_dir)

    # =====================================================

    def save(self, refactoring_result):

        controller = refactoring_result["controller"]

        generated_files = refactoring_result["generated_files"]

        controller_name = Path(controller).stem

        base_folder = self.output_dir / controller_name / "refactored"

        base_folder.mkdir(
            parents=True,
            exist_ok=True
        )

        saved_files = []

        for file in generated_files:

            relative_path = Path(file["path"])

            output_file = base_folder / relative_path

            output_file.parent.mkdir(
                parents=True,
                exist_ok=True
            )

            with open(
                output_file,
                "w",
                encoding="utf-8"
            ) as f:

                f.write(file["content"])

            saved_files.append(str(output_file))

        return {

            "folder": str(base_folder),

            "files": saved_files

        }