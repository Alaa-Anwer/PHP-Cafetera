
<html>


<style>

/* The Modal Background */
.modal {
  display: none; /* Hidden by default */
  position: fixed; 
  z-index: 1000; 
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%;          
  overflow: auto; 
  background-color: rgba(0,0,0,0.5); /* Semi-transparent black */
}

/* Modal Box */
.modal-content {
  background-color: #fff;
  margin: 10% auto; /* 10% from top, centered */
  padding: 20px;
  border-radius: 10px;
  width: 50%; /* adjust as needed */
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  position: relative;
}

/* Close Button */
.close {
  color: #aaa;
  position: absolute;
  top: 10px;
  right: 20px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover {
  color: #000;
}

    
</style>

<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Form Title</h2>
    <form action="submit.php" method="post">
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" required>
      <br><br>
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>
      <br><br>
      <button type="submit">Submit</button>
    </form>
  </div>
</div>
</html>
