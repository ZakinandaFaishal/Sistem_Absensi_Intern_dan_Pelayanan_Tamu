import './bootstrap';

import Alpine from 'alpinejs';
import QRCode from 'qrcode';

window.Alpine = Alpine;

Alpine.start();

async function setupKioskQr() {
	const canvas = document.getElementById('kiosk-qr');
	if (!canvas) return;

	const endpoint = canvas.dataset.tokenEndpoint;
	const locationSelect = document.getElementById('kiosk-location');
	const urlEl = document.getElementById('kiosk-scan-url');
	const expiresEl = document.getElementById('kiosk-expires');

	async function refresh() {
		const locationId = locationSelect?.value;
		if (!endpoint || !locationId) return;

		const res = await fetch(endpoint, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
				'Accept': 'application/json',
			},
			body: JSON.stringify({ location_id: locationId }),
		});

		if (!res.ok) return;
		const data = await res.json();

		await QRCode.toCanvas(canvas, data.scan_url, { width: 320, margin: 1 });
		if (urlEl) urlEl.textContent = data.scan_url;
		if (expiresEl) expiresEl.textContent = String(data.expires_in);
	}

	await refresh();
	setInterval(refresh, 5000);
	locationSelect?.addEventListener('change', refresh);
}

setupKioskQr();
