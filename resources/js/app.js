import './bootstrap';

import Alpine from 'alpinejs';
import QRCode from 'qrcode';

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
