# SimpleRest Jobs

`boctulus/simplerest-jobs` groups the background-service base class, cron job
helpers, and database-backed task support used by SimpleRest applications.
The host application remains responsible for scheduling and running work.

The existing classes are `BackgroundService`, `CronJob`,
`CronJobMananger` (the historical spelling), and `Task`, all under
`Boctulus\Simplerest\Core\Libs`. `Task` and cron management use SimpleRest
core services; this package does not install a scheduler or a queue backend.
