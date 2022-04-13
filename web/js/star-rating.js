const ratingInput = document.querySelector('.rating-input');

document.querySelectorAll('.stars-rating--interactive .rating-item').forEach((ratingItem) => {
  ratingItem.addEventListener('click', () => {
    const itemValue = ratingItem.dataset.itemValue;
    ratingItem.parentNode.dataset.totalValue = itemValue;
    ratingInput.value = itemValue;
    ratingInput.dispatchEvent(new Event('change'));
  });
});
