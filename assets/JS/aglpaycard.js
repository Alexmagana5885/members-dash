
  // Initialize Stripe
  const stripe = Stripe('pk_test_51PqsfU2MYhHrCbQGn5xyfKQ4lKf7EOFZpy1fqlIBEMNQdzdtcCvKDuEdhMFoCs69hRg0WSn8atc1NMQ9HIkQpV9v00rcyccmOx'); // publishable key
  const elements = stripe.elements();
  const cardElement = elements.create('card');

  // Mount the card element
  cardElement.mount('#card-element');

  // Handle form submission
  const form = document.getElementById('payment-form');
  const cardButton = document.getElementById('card-button');

  cardButton.addEventListener('click', async (event) => {
    event.preventDefault();

    const { error, paymentMethod } = await stripe.createPaymentMethod({
      type: 'card',
      card: cardElement,
    });

    if (error) {
      // Show error to your customer
      document.getElementById('card-errors').textContent = error.message;
    } else {
      // Send the paymentMethod.id to your server (use fetch or XMLHttpRequest)
      fetch('/create-payment-intent', { // Your server endpoint
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ paymentMethodId: paymentMethod.id })
      }).then(response => response.json()).then(response => {
        if (response.error) {
          document.getElementById('card-errors').textContent = response.error;
        } else {
          // The payment has been processed!
          console.log('Payment successful!');
        }
      });
    }
  });

