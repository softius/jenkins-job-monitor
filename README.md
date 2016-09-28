# Jenkins job monitor

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Jenkins is useful for monitoring the non-interactive execution of processes, such as cron jobs, procmail, inetd-launched processes. This library facilates Jenkins integration for PHP projects and processes running in a PHP environment.

[Monitoring external jobs in Jenkins][link-external-monitor-job]

## Install

Via Composer

``` bash
$ composer require softius/jenkins-job-monitor
```

## Usage

### Monitor a process

A process result can be submitted using the command `jenkins-job-monitor monitor` as indicated below.

``` bash
jenkins-job-monitor monitor http://acme.org/jenkins jobName 'ls -lah'
```

### Submit a process result

A process result can be submitted using the command `jenkins-job-monitor push`. This approach is only useful when the process output and total execution are already available and it's only necessary to push the data to Jenkins.

``` bash
jenkins-job-monitor push http://acme.org/jenkins jobName --log "Command results" --duration 5
```
Large log results can be transported through pipe as shown below.

``` bash
cat results.txt | jenkins-job-monitor push http://acme.org/jenkins jobName --duration 5
```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email softius@gmail.com instead of using the issue tracker.

## Credits

- [Iacovos Constantinou][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/softius/jenkins-job-monitor.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/softius/jenkins-job-monitor/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/softius/jenkins-job-monitor.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/softius/jenkins-job-monitor.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/softius/jenkins-job-monitor.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/softius/jenkins-job-monitor
[link-travis]: https://travis-ci.org/softius/jenkins-job-monitor
[link-scrutinizer]: https://scrutinizer-ci.com/g/softius/jenkins-job-monitor/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/softius/jenkins-job-monitor
[link-downloads]: https://packagist.org/packages/softius/jenkins-job-monitor
[link-author]: https://github.com/softius
[link-contributors]: ../../contributors
[link-external-monitor-job]: https://wiki.jenkins-ci.org/display/JENKINS/Monitoring+external+jobs
