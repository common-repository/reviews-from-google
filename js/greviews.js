(function add_read_more() {
  // get all the content divs..
  let contentDivs = document.querySelectorAll('.greview-content');  

  // limit them to display only max 140 characters at first
  contentDivs.forEach(div => {
    let fullContent = div.innerHTML;
    let shortenedContent = div.innerHTML.substring(0, 140);

    let readMoreDiv = document.createElement('div');
    readMoreDiv.className = "greview-read-more";
    
    let readMoreLink = document.createElement('a');
    readMoreLink.innerHTML = 'Read more'; 

    let readLessLink = document.createElement('a');
    readLessLink.innerHTML = 'Read less'; 

    let currentState = 'full';

    function toggleContent(e = null) {
      if(e) {
        e.preventDefault();
      }

      if(currentState === 'shortened') {
        div.innerHTML = fullContent + '...';
        currentState = 'full';
        div.append(readLessLink);
      } else {
        div.innerHTML = shortenedContent + '...';
        currentState = 'shortened';
        div.append(readMoreLink);
      }
    }

    toggleContent();

    readMoreLink.addEventListener('click', toggleContent);
    readLessLink.addEventListener('click', toggleContent);
  })
})()