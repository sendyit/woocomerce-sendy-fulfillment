<style>

.wrap ul {
    list-style: circle;
    margin-left: 40px;
}

.faq-image{ height: 400px; margin-left: 100px; margin-top: 20px; margin-bottom: 30px; display: inline-block;}



</style>

 <div class="wrap">



<h1>My products are not synching</h1>
<ul><li>
Ensure that you fill in the price & name of a product when adding or editing it.
These are required field for the api api key
</li>
<li>
You can also check the products table in the woocomerce tab, if the products are not synching. It will show you the
products that synched and ones that did not
</li>
</ul>

  <hr/>

  <h1> How to define how much the rider will collect from your customer</h1>
  <ul>
<li> If this setting <strong> Include Collect Amount </strong> is set in the <strong>Orders
</strong>
tab, we will include a message to the rider to collect payment from the customer </li>
    <li>
  The amount to collect is the total amount indicated at the checkout page.The partner will
  get the a notification to collect the total amount as shown below

  <img class="faq-image" src ="https://images.sendyit.com/fulfilment/seller/total_amount.png" />



  </li>
  <li>
To configure the shipping cost please do it here ->
<a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=wc-settings&tab=shipping" >
  shipping cost </a>
  </li>

</ul>
    <hr/>

    <h1> Supported Currencies</h1>
    <ul><li>
    Supported currencies are for the following countries; Kenya, Uganda, Tanzania, Naira &
    CFA in Ivory Coast(Côte d'Ivoire)



    </li>
    <li>
  To configure the Currencies please set them here ->
  <a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=wc-settings&tab=general" >
    Currencies  </a>
    </li>

  </ul>
      <hr/>

      <h1> I have added products in woocomerce & sendy fulfillment separately, what will happen?</h1>
      <ul><li>


Products & orders will not synch corretly since there was no api_key and username linking
sendy fulfillment to your wordpress account. Please reach out to us on <a href="mailto:merchantapi@sendyit.com">merchantapi@sendyit.com</a>
and we can help you resolve this.

      </li>


    </ul>
        <hr/>

        <h1> My orders are not being created after switching environments</h1>
      <ul><li>


        If you had been using the test environment to add products then they have not synced to your live account. This will cause the order placement process to fail if you have switched to the live environment. To avoid
         this please click on the sync all products button in the inventory tab to make sure your products are available on you live account.

      </li>


    </ul>
        <hr/>

    <h1> I just activated the plugin but my orders are not being created</h1>
      <ul><li>
        If you have just installed the plugin, navigate to the settings page ->
        <a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=sendy-fulfillment&tab=general" >
          Settings  </a>.
        Enter your business details and save the changes.

      </li>
      <li>
      Proceed to the Inventory Settings page ->
        <a href="<?php echo get_site_url();?>/wp-admin/admin.php?page=sendy-fulfillment&tab=inventory" >
          Inventory Settings  </a>.
        Click on `Sync All Products` button to sync your store's products to Sendy Fulfillment.
      </li>
      <li>
      You are now ready to create orders!
      </li>


    </ul>
        <hr/>


 </div>
