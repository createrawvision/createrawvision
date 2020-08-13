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

const matches = (text, search) => {
  // Ignore words with less than 3 charaters
  const searchWords = search.toLowerCase().split(' ').filter(word => word.length > 3);
  
  // When no search, don't filter
  if(searchWords.length === 0) return true;
  
  // Return when at least 60% of words match (ignoring case)
  text = text.toLowerCase();
  const searchHits = searchWords.filter(searchWord => text.includes(searchWord)).length;
  const matchRatio = searchHits / searchWords.length;
  return matchRatio > 0.599;
}

const searchInput = document.querySelector('#faq-searchform');
if( searchInput ) {
  const searchForm = searchInput.parentElement;

  // filter faqs once, when input is prefilled
  filterFaqs(searchInput.value); 

  // ... and filter on each input
  searchInput.addEventListener("input", () => filterFaqs(searchInput.value));

  // prevent search submission when JS is active
  searchForm.addEventListener("submit", event => event.preventDefault());
}
