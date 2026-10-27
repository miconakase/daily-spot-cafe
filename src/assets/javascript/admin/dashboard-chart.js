// Daily gross sales column chart (single series, so no legend: the card title names it).
// Expects DAILY_SALES = [{date, label, sales, orders}, ...] oldest first.
(function() {
    const container = document.getElementById('dailyChart');
    const tooltip = document.getElementById('chartTooltip');
    if (!container || typeof DAILY_SALES === 'undefined') return;

    const SVG_NS = 'http://www.w3.org/2000/svg';
    const MARGIN = { top: 12, right: 8, bottom: 26, left: 56 };
    const MAX_BAR = 24;     // bars never fill the slot
    const RADIUS = 4;       // rounded data end, square at the baseline

    const peso = n => '₱' + Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const pesoAxis = n => '₱' + Number(n).toLocaleString('en-PH', { maximumFractionDigits: 0 });

    function el(name, attrs) {
        const node = document.createElementNS(SVG_NS, name);
        for (const key in attrs) node.setAttribute(key, attrs[key]);
        return node;
    }

    // Round the axis max up to 1, 2, 2.5 or 5 × 10^n so ticks are clean numbers
    function niceMax(value) {
        if (value <= 0) return 100;
        const exp = Math.pow(10, Math.floor(Math.log10(value)));
        const step = [1, 2, 2.5, 5, 10].find(s => s * exp >= value);
        return step * exp;
    }

    // Column path with rounded top corners only
    function columnPath(x, y, w, h) {
        const r = Math.min(RADIUS, w / 2, h);
        return `M${x},${y + h} V${y + r} Q${x},${y} ${x + r},${y} H${x + w - r} Q${x + w},${y} ${x + w},${y + r} V${y + h} Z`;
    }

    function render() {
        container.querySelectorAll('svg, .chart-empty').forEach(n => n.remove());

        const width = container.clientWidth;
        const height = container.clientHeight;
        const plotW = width - MARGIN.left - MARGIN.right;
        const plotH = height - MARGIN.top - MARGIN.bottom;
        const maxSales = Math.max(...DAILY_SALES.map(d => d.sales));
        const yMax = niceMax(maxSales);
        const y = v => MARGIN.top + plotH - (v / yMax) * plotH;

        const svg = el('svg', { viewBox: `0 0 ${width} ${height}`, 'aria-hidden': 'false' });

        // Gridlines and y ticks (4 intervals)
        for (let i = 0; i <= 4; i++) {
            const value = (yMax / 4) * i;
            const gy = y(value);
            svg.appendChild(el('line', { x1: MARGIN.left, x2: width - MARGIN.right, y1: gy, y2: gy, class: 'chart-grid' }));
            const label = el('text', { x: MARGIN.left - 8, y: gy + 4, 'text-anchor': 'end', class: 'chart-axis-text' });
            label.textContent = pesoAxis(value);
            svg.appendChild(label);
        }

        const slot = plotW / DAILY_SALES.length;
        const barW = Math.min(MAX_BAR, Math.max(4, slot - 6));
        const labelEvery = Math.ceil(DAILY_SALES.length / 8);

        DAILY_SALES.forEach((day, i) => {
            const slotX = MARGIN.left + i * slot;
            const barX = slotX + (slot - barW) / 2;
            const barTop = y(day.sales);
            const barH = MARGIN.top + plotH - barTop;

            // Hit target is the whole column slot, bigger than the bar itself
            const hit = el('rect', {
                x: slotX, y: MARGIN.top, width: slot, height: plotH, class: 'chart-hit', tabindex: 0,
                'aria-label': `${day.label}: ${peso(day.sales)}, ${day.orders} order${day.orders === 1 ? '' : 's'}`
            });
            svg.appendChild(hit);

            const bar = day.sales > 0
                ? el('path', { d: columnPath(barX, barTop, barW, barH), class: 'chart-bar' })
                : el('rect', { x: barX, y: MARGIN.top + plotH - 1, width: barW, height: 1, class: 'chart-bar', opacity: 0.35 });
            svg.appendChild(bar);

            const show = () => showTooltip(day, slotX + slot / 2, Math.min(barTop, MARGIN.top + plotH - 4), bar);
            hit.addEventListener('pointerenter', show);
            hit.addEventListener('focus', show);
            hit.addEventListener('pointerleave', () => hideTooltip(bar));
            hit.addEventListener('blur', () => hideTooltip(bar));

            // Selective x labels; always label the most recent day
            if (i % labelEvery === 0 || i === DAILY_SALES.length - 1) {
                if (i !== DAILY_SALES.length - 1 && DAILY_SALES.length - 1 - i < labelEvery) return;
                const xl = el('text', { x: slotX + slot / 2, y: height - 6, 'text-anchor': 'middle', class: 'chart-axis-text' });
                xl.textContent = i === DAILY_SALES.length - 1 ? 'Today' : day.label;
                svg.appendChild(xl);
            }
        });

        container.insertBefore(svg, tooltip);

        if (maxSales === 0) {
            const empty = document.createElement('div');
            empty.className = 'chart-empty';
            empty.textContent = 'No sales in this period yet';
            container.appendChild(empty);
        }
    }

    function showTooltip(day, x, top, bar) {
        container.querySelectorAll('.chart-bar.is-active').forEach(b => b.classList.remove('is-active'));
        bar.classList.add('is-active');
        tooltip.replaceChildren();
        const value = document.createElement('strong');
        value.textContent = peso(day.sales);
        const meta = document.createElement('span');
        meta.textContent = `${day.label} · ${day.orders} order${day.orders === 1 ? '' : 's'}`;
        tooltip.append(value, meta);
        tooltip.style.display = 'block';
        tooltip.style.left = Math.min(Math.max(x, 60), container.clientWidth - 60) + 'px';
        tooltip.style.top = (top - 8) + 'px';
    }

    function hideTooltip(bar) {
        bar.classList.remove('is-active');
        tooltip.style.display = 'none';
    }

    render();
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(render, 150);
    });
})();
