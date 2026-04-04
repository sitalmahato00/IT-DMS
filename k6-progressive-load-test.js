import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

// Custom metrics
const successRate = new Rate('success_rate');
const responseTime = new Trend('response_time_ms');
const errorCount = new Counter('error_count');
const p95Latency = new Trend('p95_latency');

export const options = {
  stages: [
    { duration: '1m', target: 10, desc: '10 users ramp' },
    { duration: '2m', target: 10, desc: '10 users soak' },
    { duration: '1m', target: 50, desc: '50 users ramp' },
    { duration: '2m', target: 50, desc: '50 users soak' },
    { duration: '1m', target: 100, desc: '100 users ramp' },
    { duration: '2m', target: 100, desc: '100 users soak' },
    { duration: '1m', target: 500, desc: '500 users ramp' },
    { duration: '2m', target: 500, desc: '500 users soak' },
    { duration: '1m', target: 1000, desc: '1000 users ramp' },
    { duration: '2m', target: 1000, desc: '1000 users soak' },
    { duration: '1m', target: 0, desc: 'Cooldown' },
  ],
  thresholds: {
    http_req_duration: [
      'p(95)<2000',  // P95 < 2 seconds
      'p(99)<5000',  // P99 < 5 seconds
    ],
    http_req_failed: ['rate<0.05'],  // Error rate < 5%
    'success_rate': ['rate>0.95'],
  },
};

// Critical API endpoints to test
const endpoints = [
  { url: '/admin/dashboard', name: 'Dashboard' },
  { url: '/admin/exam', name: 'Exams' },
  { url: '/admin/semesters', name: 'Semesters' },
  { url: '/admin/students', name: 'Students' },
  { url: '/admin/notices', name: 'Notices' },
];

export default function () {
  // Test each endpoint in sequence
  for (const endpoint of endpoints) {
    group(`Testing: ${endpoint.name}`, () => {
      const res = http.get(`http://localhost:8000${endpoint.url}`, {
        tags: { name: endpoint.name },
      });

      const success = res.status === 200;
      successRate.add(success);
      responseTime.add(res.timings.duration);
      p95Latency.add(res.timings.duration);
      if (!success) errorCount.add(1);

      check(res, {
        [`${endpoint.name}: status 200`]: (r) => r.status === 200,
        [`${endpoint.name}: response time < 5s`]: (r) => r.timings.duration < 5000,
        [`${endpoint.name}: body exists`]: (r) => r.body.length > 0,
        [`${endpoint.name}: no 5xx errors`]: (r) => r.status < 500,
      });
    });

    sleep(0.5);
  }
}
