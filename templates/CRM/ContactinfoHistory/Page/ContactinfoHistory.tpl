<div class="crm-contactinfo-history">

  {if $addressHistory}
    <div class="crm-history-section">
      <h3>{ts}Address History{/ts}</h3>
      <table class="selector row-highlight">
        <thead>
          <tr>
            <th>{ts}Location{/ts}</th>
            <th>{ts}Address{/ts}</th>
            <th>{ts}City{/ts}</th>
            <th>{ts}State/Province{/ts}</th>
            <th>{ts}Postal Code{/ts}</th>
            <th>{ts}Country{/ts}</th>
            <th>{ts}Primary{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            {if $canManage}<th>{ts}Orig Contact{/ts}</th>{/if}
          </tr>
        </thead>
        <tbody>
          {foreach from=$addressHistory item=address}
            <tr class="{if $address.end_date}disabled{/if}">
              <td>{$address.location_type}</td>
              <td>{$address.street_address}</td>
              <td>{$address.city}</td>
              <td>{$address.state_province}</td>
              <td>{$address.postal_code}</td>
              <td>{$address.country}</td>
              <td>{if $address.is_primary}{ts}Yes{/ts}{/if}</td>
              <td>{$address.start_date|crmDate}</td>
              <td>{if $address.end_date}{$address.end_date|crmDate}{else}<strong>{ts}Current{/ts}</strong>{/if}</td>
              {if $canManage}
                <td>
                  {if $address.orig_contact_id && $address.orig_contact_id != $address.contact_id}
                    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$address.orig_contact_id`"}">{$address.orig_contact_id}</a>
                  {/if}
                </td>
              {/if}
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  {/if}

  {if $emailHistory}
    <div class="crm-history-section">
      <h3>{ts}Email History{/ts}</h3>
      <table class="selector row-highlight">
        <thead>
          <tr>
            <th>{ts}Location{/ts}</th>
            <th>{ts}Email{/ts}</th>
            <th>{ts}Primary{/ts}</th>
            <th>{ts}Billing{/ts}</th>
            <th>{ts}On Hold{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            {if $canManage}<th>{ts}Orig Contact{/ts}</th>{/if}
          </tr>
        </thead>
        <tbody>
          {foreach from=$emailHistory item=email}
            <tr class="{if $email.end_date}disabled{/if}">
              <td>{$email.location_type}</td>
              <td>{$email.email}</td>
              <td>{if $email.is_primary}{ts}Yes{/ts}{/if}</td>
              <td>{if $email.is_billing}{ts}Yes{/ts}{/if}</td>
              <td>{if $email.on_hold}{ts}Yes{/ts}{/if}</td>
              <td>{$email.start_date|crmDate}</td>
              <td>{if $email.end_date}{$email.end_date|crmDate}{else}<strong>{ts}Current{/ts}</strong>{/if}</td>
              {if $canManage}
                <td>
                  {if $email.orig_contact_id && $email.orig_contact_id != $email.contact_id}
                    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$email.orig_contact_id`"}">{$email.orig_contact_id}</a>
                  {/if}
                </td>
              {/if}
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  {/if}

  {if $phoneHistory}
    <div class="crm-history-section">
      <h3>{ts}Phone History{/ts}</h3>
      <table class="selector row-highlight">
        <thead>
          <tr>
            <th>{ts}Location{/ts}</th>
            <th>{ts}Phone{/ts}</th>
            <th>{ts}Extension{/ts}</th>
            <th>{ts}Type{/ts}</th>
            <th>{ts}Primary{/ts}</th>
            <th>{ts}Start Date{/ts}</th>
            <th>{ts}End Date{/ts}</th>
            {if $canManage}<th>{ts}Orig Contact{/ts}</th>{/if}
          </tr>
        </thead>
        <tbody>
          {foreach from=$phoneHistory item=phone}
            <tr class="{if $phone.end_date}disabled{/if}">
              <td>{$phone.location_type}</td>
              <td>{$phone.phone}</td>
              <td>{$phone.phone_ext}</td>
              <td>{$phone.phone_type}</td>
              <td>{if $phone.is_primary}{ts}Yes{/ts}{/if}</td>
              <td>{$phone.start_date|crmDate}</td>
              <td>{if $phone.end_date}{$phone.end_date|crmDate}{else}<strong>{ts}Current{/ts}</strong>{/if}</td>
              {if $canManage}
                <td>
                  {if $phone.orig_contact_id && $phone.orig_contact_id != $phone.contact_id}
                    <a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$phone.orig_contact_id`"}">{$phone.orig_contact_id}</a>
                  {/if}
                </td>
              {/if}
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  {/if}

  {if !$addressHistory && !$emailHistory && !$phoneHistory}
    <div class="messages status no-popup">
      {ts}No contact history found.{/ts}
    </div>
  {/if}

</div>

<style>
{literal}
.crm-contactinfo-history .crm-history-section {
  margin-bottom: 2em;
}
.crm-contactinfo-history tr.disabled {
  opacity: 0.6;
}
{/literal}
</style>
