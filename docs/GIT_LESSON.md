# Git and publishing

## Publish the initial repository

Create an empty GitHub repository without initializing a README or license. In this project's directory, review the files before making the first commit:

```sh
git status --short
git add .
git diff --cached --stat
git diff --cached
git commit -m "Prepare Laravel training project for public use"
git branch -M main
```

`git add` stages a snapshot; `git diff --cached` reviews exactly what the commit will contain. The ignore rules exclude `.env`, Composer credentials, dependencies, local databases, runtime caches, logs, and the local Composer binary. Keep `composer.lock` committed so learners install the same dependency versions. Review custom files for secrets as well; ignore rules are not a secret scanner.

Replace `YOUR-ACCOUNT` and `YOUR-REPOSITORY` with the repository you created:

```sh
git remote add origin https://github.com/YOUR-ACCOUNT/YOUR-REPOSITORY.git
git push -u origin main
```

If `origin` already exists, inspect `git remote -v` before changing it. A commit is local; `push` uploads it. Set the GitHub repository visibility to public when you are ready to share it. Check the Actions tab after the first push.

## Trainer workflow

```sh
git clone https://github.com/YOUR-ACCOUNT/YOUR-REPOSITORY.git
cd YOUR-REPOSITORY
git switch -c lesson/improve-task-copy
```

Follow the setup guide, change the Blade introduction, and inspect `git diff`. Stage the changed file and commit with a purpose-focused message. Use `git show` to explain the recorded change. For contributions, fork the repository first and open a pull request from a branch in your fork.

Avoid destructive reset/clean exercises in the real project. See [CONTRIBUTING.md](../CONTRIBUTING.md) for the checks to run before a pull request.
