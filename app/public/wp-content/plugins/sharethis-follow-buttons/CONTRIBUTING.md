# Contributing

1. `main` is a protected branch (no one can commit or push to it). To contribute changes, branch off of main and make a pull request back to it.
1. To create a branch, always `git checkout main` and then `git checkout -b [your-branch-name]`. Typically branch names will include the ticket number they're aiming to resolve (e.g. `PP-9999-fix-header-nav`).
1. Commits should be prefixed with the ticket number like so: `PP-9999: Fix header nav.`
1. Once you're happy with your branch and think it's ready for code review, commit your changes, push them up and create a PR (pull request).
1. Make sure the PR title is well written, includes the ticket number, but is concise (short and to the point).
1. Make sure the PR description describes the problem, the resolution and links to the Jira ticket.
1. Request code review.
1. Once the code review is approved, your branch will be merged by the repo maintainer and included in release candidates and releases.