#!/bin/bash

function git_diff_with_hash() {
  local num_of_commits_ahead=$1
  local commit_hash=$(git rev-parse HEAD~$num_of_commits_ahead)

  echo "Commit Hash: $commit_hash"
  git diff --name-only HEAD HEAD~$num_of_commits_ahead
}

git_diff_with_hash "$@"
