from __future__ import annotations

import logging
import re
from pathlib import Path
from typing import Dict, List


logger = logging.getLogger(__name__)


class LaravelReader:
    """
    Service untuk membaca source code Laravel.

    Digunakan sebagai komponen awal pada workflow Multi-Agent
    sebelum source code dikirim ke Analysis Agent.
    """

    def __init__(self, laravel_project_path: str | Path):
        self.project_path = Path(laravel_project_path).resolve()

        if not self.project_path.exists():
            raise FileNotFoundError(
                f"Laravel project tidak ditemukan:\n{self.project_path}"
            )

        self.controller_dir = (
            self.project_path / "app" / "Http" / "Controllers"
        )

        if not self.controller_dir.exists():
            raise FileNotFoundError(
                f"Folder Controllers tidak ditemukan:\n{self.controller_dir}"
            )

        print("\n==========================================")
        print("LARAVEL READER")
        print("==========================================")
        print(f"\nProject Path")
        print(f"  {self.project_path}")
        print(f"\nController Directory")
        print(f"  {self.controller_dir}")

        logger.info("Laravel Project : %s", self.project_path)
        logger.info("Controller Path : %s", self.controller_dir)

    # ==========================================================
    # BASIC
    # ==========================================================

    def get_controller_directory(self) -> Path:
        """
        Mengembalikan direktori Controller Laravel.
        """
        return self.controller_dir

    # ==========================================================
    # LIST CONTROLLER
    # ==========================================================

    def get_all_controllers(self) -> List[str]:
        """
        Mengambil seluruh file Controller secara recursive.

        Returns
        -------
        List[str]
            List path relatif.
        """

        controllers = [
            str(file.relative_to(self.controller_dir))
            for file in self.controller_dir.rglob("*Controller.php")
        ]

        controllers.sort()

        print("\nScanning Controllers...\n")

        for controller in controllers:
            print(f"✓ {controller}")

        print(f"\nTotal Controller : {len(controllers)}")

        logger.info("Found %s controller(s)", len(controllers))

        return controllers

    # ==========================================================
    # EXIST
    # ==========================================================

    def controller_exists(self, relative_path: str) -> bool:
        """
        Mengecek apakah controller tersedia.
        """

        controller = self.controller_dir / relative_path

        return controller.exists()

    # ==========================================================
    # READ
    # ==========================================================

    def read_controller(self, relative_path: str) -> str:
        """
        Membaca source code controller.

        Parameters
        ----------
        relative_path : str

        Returns
        -------
        str
        """

        controller = self.controller_dir / relative_path

        if not controller.exists():
            raise FileNotFoundError(
                f"Controller tidak ditemukan:\n{relative_path}"
            )

        logger.info("Reading %s", relative_path)

        print(f"\nReading Controller")
        print(f"✓ {relative_path}")

        return controller.read_text(
            encoding="utf-8",
            errors="ignore",
        )

    # ==========================================================
    # MULTIPLE READ
    # ==========================================================

    def read_multiple_controllers(
        self,
        controller_list: List[str],
    ) -> Dict[str, str]:
        """
        Membaca banyak controller sekaligus.

        Returns
        -------
        Dict[str,str]
        """

        result = {}

        for controller in controller_list:

            print(f"Loading {controller}")

            result[controller] = self.read_controller(controller)

        return result

    # ==========================================================
    # INFORMATION
    # ==========================================================

    @staticmethod
    def get_controller_name(relative_path: str) -> str:
        """
        Mengambil nama controller.

        Example
        -------
        Admin/GajiController.php

        ->
        GajiController
        """

        return Path(relative_path).stem

    # ==========================================================
    # NAMESPACE
    # ==========================================================

    def get_namespace(self, relative_path: str) -> str | None:
        """
        Mengambil namespace controller.
        """

        source = self.read_controller(relative_path)

        match = re.search(
            r"namespace\s+([^;]+);",
            source,
        )

        if match:
            return match.group(1).strip()

        return None

    # ==========================================================
    # CLASS
    # ==========================================================

    def get_class_name(self, relative_path: str) -> str | None:
        """
        Mengambil nama class controller.
        """

        source = self.read_controller(relative_path)

        match = re.search(
            r"class\s+(\w+)",
            source,
        )

        if match:
            return match.group(1)

        return None

    # ==========================================================
    # FULL INFORMATION
    # ==========================================================

    def get_controller_information(
        self,
        relative_path: str,
    ) -> Dict:
        """
        Mengambil informasi lengkap controller.

        Digunakan Analysis Agent.
        """

        return {
            "name": self.get_controller_name(relative_path),
            "namespace": self.get_namespace(relative_path),
            "class": self.get_class_name(relative_path),
            "path": relative_path,
            "source": self.read_controller(relative_path),
        }

    # ==========================================================
    # SUMMARY
    # ==========================================================

    def summary(self) -> Dict:
        """
        Ringkasan project Laravel.
        """

        controllers = self.get_all_controllers()

        return {
            "project": str(self.project_path),
            "controller_directory": str(self.controller_dir),
            "total_controller": len(controllers),
            "controllers": controllers,
        }