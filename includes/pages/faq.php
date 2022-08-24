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
<li> If this sesstin <strong> Include Collect Amount </strong> is set in the <strong>Orders
</strong>
tab, we will include a message to the rider to collect payment from the customer </li>
    <li>
  The amount to collect is the total amount indicated at the checkout page.The partner will
  get the a notification to collect the total amount as shown below

  <img class="faq-image" src ="<?php echo plugins_url('sendy-fulfillment/includes');?>/images/faq-images/total_amount.png" />



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
sendy fulfillment to your wordpress account. Please reach out to us on merchantapi@sendyit.com
and we can help you resolve this.

      </li>
      

    </ul>
        <hr/>


 </div>
