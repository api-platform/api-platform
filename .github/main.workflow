workflow "check-and-release" {
    on = "push"
    resolves = "style"
}

action "build_env" {
    uses = "./.github/actions/build_enviorement"
}

action "style" {
    needs = ["build_env"]
    uses = "./.github/actions/phpcsfixer"
}

