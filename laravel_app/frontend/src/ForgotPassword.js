import React from 'react';

const ForgotPassword = () => {
  const handleSubmit = (e) => {
    e.preventDefault();
    // Add logic for handling the forgot password form submission
  };

  return (
    <div>
      <h2>Forgot Password</h2>
      <form onSubmit={handleSubmit}>
        <label htmlFor="email">Email:</label>
        <input type="email" id="email" name="email" required />
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default ForgotPassword;
