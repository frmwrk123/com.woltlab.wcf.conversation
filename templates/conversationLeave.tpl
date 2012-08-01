<fieldset>
	<legend>{lang}wcf.conversation.hideConversation{/lang}</legend>
	
	<dl>
		{if $hideConversation == 1}
			<dd>
				<label><input type="radio" name="hideConversation" value="0" /> {lang}wcf.conversation.hideConversation.restore{/lang}</label>
			</dd>
		{/if}
		{if $hideConversation != 1}
			<dd>
				<label><input type="radio" name="hideConversation" value="1" /> {lang}wcf.conversation.hideConversation.leave{/lang}</label>
				<small>{lang}wcf.conversation.hideConversation.leave.description{/lang}</small>
			</dd>
		{/if}
		<dd>
			<label><input type="radio" name="hideConversation" value="2" /> {lang}wcf.conversation.hideConversation.leavePermanently{/lang}</label>
			<small>{lang}wcf.conversation.hideConversation.leavePermanently.description{/lang}</small>
		</dd>
	</dl>
</fieldset>

<div class="formSubmit">
	<button id="hideConversation">{lang}wcf.global.form.submit{/lang}</button>
</div>