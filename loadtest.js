import http from 'k6/http';
import { check } from 'k6';

export const options = {
    scenarios: {
        burst_100_user: {
            executor: 'per-vu-iterations',
            vus: 100,
            iterations: 1,
            maxDuration: '30s',
        },
    },
};

export default function () {
    const res = http.get(
        'http://192.168.145.74:8080',
        {
            redirects: 0,
        }
    );

    if (res.status !== 200 && res.status !== 302) {
        console.log(
            `VU=${__VU} STATUS=${res.status} TIME=${res.timings.duration}ms URL=${res.url}`
        );
    }

    check(res, {
        'status 200/302': (r) =>
            r.status === 200 || r.status === 302,

        'response < 2 detik': (r) =>
            r.timings.duration < 2000,
    });
}