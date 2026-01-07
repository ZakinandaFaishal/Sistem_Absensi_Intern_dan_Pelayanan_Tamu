import './bootstrap';

import Alpine from 'alpinejs';
import QRCode from 'qrcode';
import { Html5QrcodeScanner } from 'html5-qrcode';

window.Alpine = Alpine;

Alpine.start();

async function setupKioskQr() {
	const canvas = document.getElementById('kiosk-qr');
	if (!canvas) return;

	const endpoint = canvas.dataset.tokenEndpoint;
	const urlEl = document.getElementById('kiosk-scan-url');
	const expiresEl = document.getElementById('kiosk-expires');

	async function refresh() {
		if (!endpoint) return;

		const res = await fetch(endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				'Accept': 'application/json',
			},
			body: JSON.stringify({}),
		});

		if (!res.ok) return;
		const data = await res.json();

		await QRCode.toCanvas(canvas, data.scan_url, { width: 320, margin: 1 });
		if (urlEl) urlEl.textContent = data.scan_url;
		if (expiresEl) expiresEl.textContent = String(data.expires_in);
	}

	await refresh();
	setInterval(refresh, 5000);
}

setupKioskQr();

function setupAttendanceQrScanner() {
	const container = document.getElementById('attendance-qr-reader');
	if (!container) return;

	const scanner = new Html5QrcodeScanner(
		'attendance-qr-reader',
		{ fps: 10, qrbox: { width: 250, height: 250 } },
		false
	);

	function toTarget(decodedText) {
		const text = String(decodedText || '').trim();
		if (!text) return null;

		if (/^https?:\/\//i.test(text)) {
			return text;
		}

		const url = new URL('/presensi/scan', window.location.origin);
		url.searchParams.set('k', text);
		return url.toString();
	}

	async function onScanSuccess(decodedText) {
		const target = toTarget(decodedText);
		if (!target) return;

		try {
			await scanner.clear();
		} catch {
			// ignore
		}

		window.location.href = target;
	}

	function onScanFailure() {
		// ignore continuous scan errors
	}

	scanner.render(onScanSuccess, onScanFailure);
}

setupAttendanceQrScanner();
