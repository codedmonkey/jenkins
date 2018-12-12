# Jobs

Retrieving data for a single job is easy:

```php
$job = $jenkins->jobs->get('job-name');

$job->getDisplayName();
$job->getDescription();
```
