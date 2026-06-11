// Homepage - render featured products
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('featuredProducts');
  if (!container) return;

  const featured = PRODUCTS.filter(p => p.featured);
  container.innerHTML = featured.map(p => productCard(p)).join('');
});

function productCard(p) {
  return `
    <div class="product-card">
      <div class="product-image-wrap">
        <img src="${p.image}" alt="${p.name}" class="product-image" loading="lazy">
        ${p.featured ? '<span class="badge">Featured</span>' : ''}
      </div>
      <div class="product-info">
        <span class="product-category">${p.category}</span>
        <h3 class="product-name">${p.name}</h3>
        <p class="product-desc">${p.description}</p>
        <div class="product-footer">
          <span class="product-price">$${p.price.toFixed(2)}</span>
          <button class="btn-add" onclick="addToCart(${p.id})">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  `;
}