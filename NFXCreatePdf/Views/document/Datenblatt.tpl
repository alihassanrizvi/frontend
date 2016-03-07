
<style type="text/css">
{literal}
body {margin-top: 0px;margin-left: 0px;font-family: 'Helvetica';font-size: 12px;}
#content{
    padding-left: 30px;
}

h1{
    font: bold 16px 'Helvetica';
    line-height: 16px;
}
.tr0{height: 21px;font-size: 14px;}
.td0{border-bottom: #000000 0px solid;padding: 0px;margin: 0px;width: 330px;vertical-align: bottom;}
.td1{border-bottom: #000000 0px solid;padding: 0px;margin: 0px;width: 170px;vertical-align: bottom;}
.td2{border-bottom: #000000 0px solid;padding: 0px;margin: 0px;width: 203px;vertical-align: bottom;text-align: right;}
.ft0{
    font-weight: bold;
}
.tr4{font-size: 12px;}
.td5{padding: 0px;margin: 0px;width: 600px;vertical-align: bottom;}
.td6{padding: 0px;margin: 0px;width: 180px;vertical-align: bottom;text-align: right;}
#price{
    font: bold 16px 'Helvetica';
    line-height: 19px;
}
.artimg{
    float: left;
    margin: 20px;
    position: relative;
    width: 40%;
    height:250px;
}
#description, #description table{
    font-size: 11px;
}
.artimg img{
    height: 100%;
}
#id_2{
    border-top: 1px solid #000;
}
#info_text{
    font-size: 11px;
}
.artinfo{
    font-size: 12px;
}
{/literal}

</style>

<body>
    <div id="content">
        <div id="id_1"> <img class="header" src="{$pdf_header_image|replace:$host:''}"> </div>
        
        <h1>{$article.articleName}</h1>
        
        <table style="width: 600px;" cellspacing="10">
            <tbody>
                <tr>
                    
                    <td style="width: 300px; vertical-align: top;">{if $article.image.thumbnails[0].source}<img src="{$article.image.thumbnails[0].source|replace:$host:''}" width="255px">{/if}</td>
                    <td style="width: 500px;">
                        {if $article.price}<div id="price">{$article.price} EUR *</div>{/if}
                        <div id="info_text">* Preise inkl. gesetzlicher MwSt. zzgl.Versandkosten</div><br>
                        <p class="artinfo">
                        Marke: {$article.supplierName} <br>
                        Bestell-Nr.: {$article.ordernumber}
                        </p>
                        {if $article.additionaltext}
                            <ul>
                                <li>{$article.additionaltext}</li>
                                {foreach from=$article.sVariants item=sV}
                                    <li>{$sV.additionaltext}</li>
                                {/foreach}
                            </ul>
                        {/if}   

                    </td>
                </tr>
            </tbody>
        </table>

        <div id="description">
            {if $article.description_long}{$article.description_long|replace:"../":""}<br />{/if}
            
                {if $article.sProperties}<b>Artikeleigenschaften</b>
                <table cellspacing="0">
                    {foreach from=$article.sProperties item=sProperty}
                    <tr>
                        <td width="150px">{$sProperty.name}:</td>
                        <td>{$sProperty.value}</td>
                    </tr>   
                    {/foreach}
                </table>
                <br />
                {/if}
        </div>
        
            {if $article.sConfigurator}
            <b>Verfügbare Artikelvarianten</b>
            <table cellpadding="10">
                <tr>
                    {foreach from=$article.sConfigurator item=sC}
                        <td style="vertical-align: top; border:1px solid #C7C7C7">
                            {$sC.groupname}
                                <ul>
                                    {foreach from=$sC.values item=sV}
                                        <li>{$sV.optionname}</li>
                                    {/foreach}
                                </ul>
                        </td>
                    {/foreach}
                </tr>
            </table>
            <br />
            {/if}

            {if $article.sAccessories}
            <b>Verfügbare Zubehör-Artikel</b>
            <table cellpadding="10" border="0">
                <tr>
                    {foreach from=$article.sAccessories item=sAccess}
                        <td style="vertical-align: top; border:1px solid #C7C7C7">
                            {$sAccess.groupname}
                                <ul>
                                    {foreach from=$sAccess.childs item=sC}
                                        <li>{$sC.optionname} ({$sC.ordernumber})</li>
                                    {/foreach}
                                </ul>
                        </td>
                    {/foreach}
                </tr>
            </table>
            <br />
            {/if}   
            
    </div>

