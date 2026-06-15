<div class="login">
  <div class="login-brand">
    <img src="/assets/cosmax-idn-logo.jpg" alt="COSMAX Indonesia">
    <h1>QAC Trial Validation</h1>
    <p>Trial Validation System</p>
  </div>

  <form method="post">
    <?php csrf_field(); ?>

    <label>Email
      <input name="email" type="email" required placeholder="name@company.com">
    </label>

    <label>Password
      <input name="password" type="password" required placeholder="Password">
    </label>

    <button>Login</button>
  </form>

  <p class="muted">Gunakan akun yang diberikan Admin.</p>
</div>

<style>
.login-brand img {
  width: 110px;      /* ukuran logo */
  height: auto;      /* menjaga proporsi */
  display: block;
  margin: 0 auto 15px;
}
</style>