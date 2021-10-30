# Composer Workspaces

This is a simple project that adds workspace support (badly) to composer, mostly helpful for working with monorepo's with interconnected dependencies

It's current only purpose is creating a automatic repository of all the composer packages in a workspace, thus avoiding the need for making custom repositories in either your config or composer.json

## How to

Install `cijber/workspace`

```bash
composer global require cijber/workspace
```

Now create a file called `workspace.cijber.json` in the root of your monorepo and make the following structure

```
{
  "name": "My amazing project!",
  "packages": [
    "my-package",
    "."
  ]
}
```

the name is simply for decoration, the strings in the packages array are paths to packages, it is thus possible to make a root project with child projects in child dirs.

After doing this and running composer update in your package it should show

```
[cijber/workspace] Using workspace: My amazing project!
```

after doing this you can now `composer require <namespace>/my-package` in your root package and it'll symlink it to the project in your workspace! helpful!
