// script.js

const CART_KEY = "pizza_cart";

// Load cart from localStorage
function getCart() {
  try {
    const cart = JSON.parse(localStorage.getItem(CART_KEY));
    return Array.isArray(cart)
      ? cart.filter(
          (item) => item && Array.isArray(item.toppings) && item.toppings.length,
        )
      : [];
  } catch {
    return [];
  }
}

function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount();
}

function updateCartCount() {
  const countEl = document.getElementById("cartCount");
  if (countEl) {
    countEl.textContent = getCart().length;
  }
}

// Pizza page logic
document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();

  // Cart button → go to checkout
  const cartBtn = document.getElementById("cartBtn");
  if (cartBtn) {
    cartBtn.addEventListener("click", () => {
      window.location.href = "checkout.php";
    });
  }

  // Add to cart
  const addBtn = document.getElementById("addToCartBtn");
  if (addBtn) {
    addBtn.addEventListener("click", () => {
      const checked = Array.from(
        document.querySelectorAll(".toggle input:checked"),
      ).map((cb) => cb.value);

      if (checked.length === 0) {
        alert("Please select at least one topping.");
        return;
      }

      const cart = getCart();
      cart.push({
        id: Date.now(),
        toppings: checked,
      });
      saveCart(cart);

      // Reset checkboxes
      document
        .querySelectorAll(".toggle input")
        .forEach((cb) => (cb.checked = false));

      alert("Pizza added to cart!");
    });
  }

  // Checkout page
  const cartItemsEl = document.getElementById("cartItems");
  const emptyCartEl = document.getElementById("emptyCart");
  const form = document.getElementById("checkoutForm");

  if (cartItemsEl) {
    const cart = getCart();
    if (cart.length === 0) {
      emptyCartEl.style.display = "block";
      form.style.display = "none";
    } else {
      cart.forEach((item, index) => {
        const li = document.createElement("li");
        li.textContent = `Pizza ${index + 1}: ${item.toppings.join(", ")}`;
        cartItemsEl.appendChild(li);
      });
    }
  }

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const cart = getCart();
      if (cart.length === 0) {
        alert("Cart is empty.");
        return;
      }

      const payload = {
        pizzas: cart,
        address: document.getElementById("address").value.trim(),
      };

      const submitButton = form.querySelector('button[type="submit"]');
      const errorEl = document.getElementById("checkoutError");
      submitButton.disabled = true;
      errorEl.style.display = "none";

      try {
        const res = await fetch("process_order.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        });

        const result = await res.json();

        if (result.success) {
          // Clear cart
          localStorage.removeItem(CART_KEY);
          updateCartCount();

          document.getElementById("successMessage").style.display = "block";
          document.getElementById("successMessage").textContent =
            `Order placed! Your Order ID is: ${result.order_id}`;

          form.style.display = "none";
          document.getElementById("cartSummary").style.display = "none";
        } else {
          errorEl.textContent = result.message || "Unable to place the order.";
          errorEl.style.display = "block";
        }
      } catch (err) {
        errorEl.textContent = "Network error. Please try again.";
        errorEl.style.display = "block";
      } finally {
        submitButton.disabled = false;
      }
    });
  }
});
