const handleFaqClick = event => {
  const faqTitle = event.target;
  const faqBody = faqTitle.nextElementSibling;

  faqTitle.classList.toggle('faq__title--open');
  faqBody.classList.toggle('faq__body--open');
}

const faqTitles = document.querySelectorAll(".faq__title");
faqTitles.forEach(faqTitle => faqTitle.addEventListener("click", handleFaqClick));

const filterFaqs = value => {
  const faqItems = document.querySelectorAll('.faq__item');
  faqItems.forEach(faqItem => {
    if(matches(faqItem.textContent, value)) {
      faqItem.classList.remove('faq__item--hidden');
    } else {
      faqItem.classList.add('faq__item--hidden');
    }
  });
}

const matches = (text, searchInput) => {
  return text.includes(searchInput);
}

const searchInput = document.querySelector('#faq-searchform');
searchInput.addEventListener("input", event => filterFaqs(event.target.value));
