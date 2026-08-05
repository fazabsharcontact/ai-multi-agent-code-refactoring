"""
Result Writer

Menyimpan seluruh file hasil refactoring
ke folder output dengan struktur Laravel.
"""

from pathlib import Path
import time


class ResultWriter:

    def __init__(self, output_dir="output"):

        self.output_dir = Path(output_dir)

    # =====================================================
    # Progress
    # =====================================================

    def step(self, message):

        print(message)

        time.sleep(0.2)

    # =====================================================
    # Save Generated Files
    # =====================================================

    def save(self, refactoring_result):

        controller = refactoring_result["controller"]

        generated_files = refactoring_result["generated_files"]

        controller_name = Path(controller).stem

        base_folder = (
            self.output_dir
            / controller_name
            / "refactored"
        )

        print("\n==========================================")
        print("RESULT WRITER")
        print("==========================================")

        self.step("\nPreparing output directory...")

        base_folder.mkdir(
            parents=True,
            exist_ok=True
        )

        print(f"✓ {base_folder}")

        print("\nWriting generated files...\n")

        saved_files = []

        for file in generated_files:

            relative_path = Path(file["path"])

            output_file = base_folder / relative_path

            output_file.parent.mkdir(
                parents=True,
                exist_ok=True
            )

            print(f"Creating : {relative_path}")

            with open(
                output_file,
                "w",
                encoding="utf-8"
            ) as f:

                f.write(file["content"])

            print("✓ Saved")

            saved_files.append(str(output_file))

        print("\n------------------------------------------")

        print(f"Total File : {len(saved_files)}")

        print("All generated files have been saved.")

        print("------------------------------------------")

        return {

            "folder": str(base_folder),

            "files": saved_files

        }