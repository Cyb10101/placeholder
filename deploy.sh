#!/usr/bin/env bash

loadEnvironmentVariables() {
    if [ -f ".env" ]; then
      source .env
    fi
    if [ -f ".env.local" ]; then
      source .env.local
    fi
}

checkGitMaster() {
    if [[ $(git symbolic-ref --short -q HEAD) != 'master' ]]; then
        echo 'ERROR: Git is not on branch master!'
        [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
    fi
}

checkGit() {
    if [[ $(git diff --stat) != '' ]]; then
        echo
        git status --porcelain
        echo

        read -p 'Git is dirty... Continue? [y/N] ' -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            [[ "$0" = "$BASH_SOURCE" ]] && exit 1 || return 1 # handle exits from shell or function but don't exit interactive shell
        fi
    fi
}

symfonyUpdateDatabase() {
    read -p 'Update database schema? [y/N] ' -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        ${BIN_PHP} ./bin/console doctrine:schema:update --force
    fi
}

symfonyClearCache() {
    ${BIN_PHP} ./bin/console cache:clear --no-warmup
    ${BIN_PHP} ./bin/console cache:warmup
}

loadEnvironmentVariables
BIN_PHP="${BIN_PHP:-php}"
BIN_COMPOSER="${BIN_COMPOSER:-composer}"

checkGitMaster
checkGit

git pull origin master
${BIN_PHP} ${BIN_COMPOSER} install

symfonyUpdateDatabase
symfonyClearCache

