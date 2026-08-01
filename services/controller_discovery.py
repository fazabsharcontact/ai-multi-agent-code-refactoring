"""
Controller Discovery

Mencari seluruh controller Laravel
di dalam folder app/Http/Controllers.
"""

from pathlib import Path


class ControllerDiscovery:

    def __init__(self, laravel_project_path):

        self.controller_dir = (
            Path(laravel_project_path)
            / "app"
            / "Http"
            / "Controllers"
        )

    # =====================================================

    def discover(self):

        controllers = []

        if not self.controller_dir.exists():

            return controllers

        for file in self.controller_dir.rglob("*Controller.php"):

            relative = file.relative_to(self.controller_dir)

            controllers.append(
                str(relative)
            )

        controllers.sort()

        return controllers