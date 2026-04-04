import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

// Custom metrics
const successRate = new Rate('success_rate');
const responseTime = new Trend('response_time_ms');
const errorCount = new Counter('error_count');

export const options = {
  stages: [
    { duration: '2m', target: 100, desc: '100 users baseline' },
    { duration: '3m', target: 100, desc: '100 users hold' },
    { duration: '2m', target: 500, desc: 'Ramp to 500' },
    { duration: '3m', target: 500, desc: '500 users hold - observe stability' },
    { duration: '2m', target: 1000, desc: 'Ramp to 1000' },
    { duration: '5m', target: 1000, desc: '1000 users - STRESS TEST' },
    { duration: '2m', target: 2000, desc: 'Spike to 2000 users' },
    { duration: '3m', target: 2000, desc: 'Hold at 2000 - find breaking point' },
    { duration: '2m', target: 500, desc: 'If survived, scale back' },
    { duration: '2m', target: 0, desc: 'Cooldown' },
  ],
  thresholds: {
    http_req_failed: ['rate<0.2'],  // Accept 20% error rate under extreme stress
    'success_rate': ['rate>0.8'],
  },
  ext: {
    loadimpact: {
      projectID: 3392928,
      name: 'Laravel DMS - Stress Test'
    }
  }
};

export default function () {
  group('Dashboard - Stress', () => {
    const res = http.get('http://localhost:8000/admin/dashboard', {
      tags: { name: 'Dashboard' },
      timeout: '30s',
    });

    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'dashboard: status 200': (r) => r.status === 200,
      'dashboard: response < 5s': (r) => r.timings.duration < 5000,
      'dashboard: response < 10s': (r) => r.timings.duration < 10000,
      'dashboard: not 500 error': (r) => r.status !== 500,
    });
  });

  group('Exams - Stress', () => {
    const res = http.get('http://localhost:8000/admin/exam', {
      tags: { name: 'Exams' },
      timeout: '30s',
    });

    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'exams: status 200': (r) => r.status === 200,
      'exams: response < 5s': (r) => r.timings.duration < 5000,
    });
  });

  group('Semesters - Stress', () => {
    const res = http.get('http://localhost:8000/admin/semesters', {
      tags: { name: 'Semesters' },
      timeout: '30s',
    });

    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'semesters: status 200': (r) => r.status === 200,
      'semesters: response < 5s': (r) => r.timings.duration < 5000,
    });
  });

  sleep(0.5);
}
