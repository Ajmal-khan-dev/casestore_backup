let cart = JSON.parse(localStorage.getItem("cart")) || [];

// Remove from Cart
function removeFromCart(index) {
  cart.splice(index, 1);
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCart();
  renderCartPage();
}

// Dropdown
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
if (cartItems && cartTotal) {
  cartItems.innerHTML = "";
  let total = 0;
  cart.forEach((item, index) => {
    total += item.price;
    cartItems.innerHTML += `
      <div class="cart-item">
        <p>${item.product} - ₹${item.price}</p>
        <button onclick="removeFromCart(${index})">Cancel</button>
      </div>
    `;
  });
  cartTotal.textContent = "Total: ₹" + total;
}

// Cart Page
renderCartPage();

// Render Cart Page
function renderCartPage() {
  const cartPageItems = document.getElementById("cartPageItems");
  const cartPageTotal = document.getElementById("cartPageTotal");

  if (!cartPageItems || !cartPageTotal) return;

  cartPageItems.innerHTML = "";
  let total = 0;

  if (cart.length === 0) {
    cartPageItems.innerHTML = "<p>Your cart is empty.</p>";
    cartPageTotal.textContent = "Total: ₹0";
    return;
  }

  cart.forEach((item, index) => {
    total += item.price;
    let div = document.createElement("div");
    div.classList.add("cart-item");
    div.innerHTML = `
      <span>${item.product} - ₹${item.price}</span>
      <button class="remove-btn" onclick="removeFromCart(${index})">🗑️</button>
    `;
    cartPageItems.appendChild(div);
  });

  cartPageTotal.textContent = "Total: ₹" + total;
}

// Open modal
function openModelList() {
  document.getElementById("modelModal").style.display = "block";
}

// Close modal
function closeModelList() {
  document.getElementById("modelModal").style.display = "none";
}

// ✅ Fixed Select model (now redirects to products page)
function selectModel(model) {
  // Save to localStorage (optional, keeps selection remembered)
  localStorage.setItem("selectedModel", model);

  // Redirect straight to model_products.php with the selected model
  window.location.href = "pages/model_products.php?model=" + encodeURIComponent(model);
}

// ✅ Merge window.onload so both cart + model load properly
window.onload = function () {
  if (typeof updateCart === "function") {
    updateCart();
  }
  let savedModel = localStorage.getItem("selectedModel");
  if (savedModel && document.getElementById("selectedModel")) {
    document.getElementById("selectedModel").innerText =
      "Selected Model: " + savedModel;
  }
};
