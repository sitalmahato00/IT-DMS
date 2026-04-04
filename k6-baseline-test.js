import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate, Trend, Counter } from 'k6/metrics';

// Custom metrics
const successRate = new Rate('success_rate');
const responseTime = new Trend('response_time_ms');
const errorCount = new Counter('error_count');

export const options = {
  stages: [
    { duration: '30s', target: 1, desc: '1 user (warmup)' },
    { duration: '2m', target: 1, desc: '1 user (baseline)' },
    { duration: '30s', target: 0, desc: 'Cooldown' },
  ],
  thresholds: {
    http_req_duration: ['p(95)<500', 'p(99)<1000'],
    http_req_failed: ['rate<0.1'],
    'success_rate': ['rate>0.95'],
  },
};

export default function () {
  // Test 1: Homepage
  group('1. Homepage /', () => {
    const res = http.get('http://localhost:8000/', {
      tags: { name: 'Homepage' },
    });
    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'hp: status is 200': (r) => r.status === 200,
      'hp: response < 500ms': (r) => r.timings.duration < 500,
      'hp: body not empty': (r) => r.body.length > 0,
    });
  });

  sleep(1);

  // Test 2: Dashboard
  group('2. Admin Dashboard', () => {
    const res = http.get('http://localhost:8000/admin/dashboard', {
      tags: { name: 'Dashboard' },
    });
    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'db: status is 200': (r) => r.status === 200,
      'db: response < 2s': (r) => r.timings.duration < 2000,
      'db: has content': (r) => r.body.length > 1000,
    });
  });

  sleep(1);

  // Test 3: Exams Page
  group('3. Exam Management', () => {
    const res = http.get('http://localhost:8000/admin/exam', {
      tags: { name: 'Exams' },
    });
    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'ex: status is 200': (r) => r.status === 200,
      'ex: response < 2s': (r) => r.timings.duration < 2000,
    });
  });

  sleep(1);

  // Test 4: Semesters
  group('4. Semester Management', () => {
    const res = http.get('http://localhost:8000/admin/semesters', {
      tags: { name: 'Semesters' },
    });
    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'sm: status is 200': (r) => r.status === 200,
      'sm: response < 2s': (r) => r.timings.duration < 2000,
    });
  });

  sleep(1);

  // Test 5: Students
  group('5. Student Management', () => {
    const res = http.get('http://localhost:8000/admin/students', {
      tags: { name: 'Students' },
    });
    const success = res.status === 200;
    successRate.add(success);
    responseTime.add(res.timings.duration);
    if (!success) errorCount.add(1);

    check(res, {
      'st: status is 200': (r) => r.status === 200,
      'st: response < 2s': (r) => r.timings.duration < 2000,
    });
  });

  sleep(2);
}
