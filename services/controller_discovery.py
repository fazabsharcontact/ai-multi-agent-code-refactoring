"""
Controller Discovery

Mencari seluruh Laravel Controller
di dalam folder app/Http/Controllers.
"""

from pathlib import Path


class ControllerDiscovery:

    def __init__(self, laravel_project_path):

        self.project_path = Path(laravel_project_path)

        self.controller_dir = (
            self.project_path
            / "app"
            / "Http"
            / "Controllers"
        )

    # =====================================================
    # Discover Controllers
    # =====================================================

    def discover(self):

        print("\n==========================================")
        print("CONTROLLER DISCOVERY")
        print("==========================================")

        print("\nScanning Laravel Controllers...")

        controllers = []

        if not self.controller_dir.exists():

            print("\nController directory not found.")

            return controllers

        for file in self.controller_dir.rglob("*Controller.php"):

            relative = file.relative_to(self.controller_dir)

            controllers.append(str(relative))

        controllers.sort()

        print(f"\nFound {len(controllers)} controller(s).\n")

        for i, controller in enumerate(controllers, start=1):

            print(f"[{i}] {controller}")

        print()

        return controllers