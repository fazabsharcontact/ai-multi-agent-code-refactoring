"""
GitHub Repository Service

Bertugas mengambil project Laravel dari GitHub
dan menyimpannya ke dalam workspace lokal.

Workflow

GitHub Repository
        │
        ▼
Clone / Pull
        │
        ▼
workspace/
        │
        ▼
Return Local Path
"""

from pathlib import Path
import subprocess


class GitHubRepository:

    def __init__(self, workspace="workspace"):

        self.workspace = Path(workspace)

        self.workspace.mkdir(
            parents=True,
            exist_ok=True
        )

    # =====================================================
    # Clone Repository
    # =====================================================

    def clone(self, github_url):

        repo_name = github_url.rstrip("/").split("/")[-1]

        if repo_name.endswith(".git"):
            repo_name = repo_name[:-4]

        project_path = self.workspace / repo_name

        print("\n==========================================")
        print("GITHUB REPOSITORY")
        print("==========================================")

        print(f"\nRepository : {github_url}")

        # ==========================================
        # Already Exists
        # ==========================================

        if project_path.exists():

            print("\nRepository already exists.")

            print("Updating repository...")

            subprocess.run(

                ["git", "-C", str(project_path), "pull"],

                check=True

            )

            print("✓ Repository updated.")

        # ==========================================
        # Clone
        # ==========================================

        else:

            print("\nCloning repository...")

            subprocess.run(

                [

                    "git",

                    "clone",

                    github_url,

                    str(project_path)

                ],

                check=True

            )

            print("✓ Repository cloned successfully.")

        print(f"\nWorkspace : {project_path}")

        return str(project_path)

    # =====================================================
    # Validate Laravel Project
    # =====================================================

    def validate(self, project_path):

        project = Path(project_path)

        composer = project / "composer.json"

        app = project / "app"

        routes = project / "routes"

        if not composer.exists():

            raise Exception(

                "composer.json not found."

            )

        if not app.exists():

            raise Exception(

                "Laravel app directory not found."

            )

        if not routes.exists():

            raise Exception(

                "Laravel routes directory not found."

            )

        print("\nLaravel project validated.")

        return True

    # =====================================================
    # Prepare Workspace
    # =====================================================

    def prepare(self, github_url):

        project_path = self.clone(github_url)

        self.validate(project_path)

        print("\nWorkspace is ready.")

        return project_path