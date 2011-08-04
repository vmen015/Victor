<section class="rel w-960 mlr-auto tL">
{if !empty($errors)}
    {foreach $errors as $key => $error}
        <div class="rel error tL rnd-5">
            <p>{$error}</p>
        </div>
    {/foreach}
{/if}
</section>