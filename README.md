Yrgopelag - Four Walls 

Four Walls is a hotel booking website for a fictional hotel in the Yrgopelag archipelago.

Visitors can book one of three available rooms (Economy, Standard, or Luxury) for stays in January 2026. A booking includes selecting arrival and departure dates, optional hotel features, and completing payment using a transfer code from a mock banking service. 

Selecting a room or adding extras updates the hotel’s graphics in real time, letting visitors see their choices reflected on the site. The calendar also adjusts automatically to show availability for the chosen room. All graphics were created by the author.

The system prevents overlapping bookings, applies a 10% discount for returning guests, and calculates the total price based on room, number of nights, and selected features. Successful bookings are stored in a SQLite database and confirmed with a receipt shown to the user.

The project is built using PHP, SQLite, HTML, CSS, and JavaScript, with payment handling integrated via the Yrgopelag Central Bank API.


The application is designed for desktop use only.



References:

Custom radio buttons:
https://moderncss.dev/pure-css-custom-styled-radio-buttons/

SVG integration, and loading data: 
https://www.youtube.com/watch?v=tk3ivPgOwpc&t=215s

Code review:

index.php (example lines 41–50)
Consider connecting <label> and <input> elements using the 'for' and 'id' attributes. It improves accessibility and usability.
Reference: https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/label#associating_a_label_with_a_form_control

loadData.php (file structure)
This file could be placed in its own directory (ex. data/) or inside a backend/ directory to better describe its function and improve project structure.

booking.php (lines 24–25)
You could use filter_var() + htmlspecialchars to sanitize user input to improve security and input validation.

loadData.php (line 19)
It’s not clear why array_reverse() is used on the rooms array here. A short comment explaining the intention would improve readability.

loadData.php (lines 40–43)
This loop doesn’t actually load data, which makes it slightly misleading given the file’s purpose (name). It could instead be placed where the features are used, or made into a function that lives in a shared file (ex. functions.php).

README
The README could include information about Gruzzle (since the application uses it), and how to install it. A link to the official installation would be good too.

styles.css (general)
There are many empty lines between and inside code blocks. While this is readable, reducing them slightly could improve consistency and make the file easier to scan.

styles.css (lines 135–141)
You could use CSS shorthands to reduce repetition, for example:
border: 1px solid #6b7280; or padding: 0.5rem 0.75rem;
This saves time and improves readability.

booking.php (line 125)
The discount value is hardcoded. Perhaps it could be stores in a variable or in the database instead, which would make it easier to update and maintain.

LICENSE (line 3)
The license file should contain the author’s full name.
Referance: https://license.md/licenses/mit-license/#:~:text=Full%20License%20Text.%20MIT%20License%20Copyright%20(c),copies%20or%20substantial%20portions%20of%20the%20Software.

Comments throughout the project
Comments are generally helpful, but they could focus more on why certain decisions were made rather than what the code does. This would make the reasoning behind the code clearer for those who code review.

