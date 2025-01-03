// Function to check if a cookie exists
function checkCookie(cookieName) {
  const cookies = document.cookie.split(';');
  for (const cookie of cookies) {
    const [name, value] = cookie.trim().split('=');
    if (name === cookieName) {
      return true;
    }
  }
  return false;
}

// Function to set a cookie with a given name and value
function setCookie(cookieName, cookieValue, expiresDays) {
  const d = new Date();
  d.setTime(d.getTime() + (expiresDays * 24 * 60 * 60 * 1000));
  const expires = "expires=" + d.toUTCString();
  document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
}

// Function to handle the click event
function handleClick() {
  const cookieName = "example_com_visited";
  const today = new Date().toDateString();
  
  // Check if the cookie for today's date exists
  if (!checkCookie(cookieName + "_" + today)) {
    // Set a cookie to mark that the user has visited today
    setCookie(cookieName + "_" + today, "visited", 1); // Expires in 1 day
  
    // Open a new tab and navigate to "example.com"
    window.open("https://s.shopee.co.id/3fmAM8YTI6", "_blank");
  }
}

// Add a click event listener to the entire document
document.addEventListener("click", handleClick);
