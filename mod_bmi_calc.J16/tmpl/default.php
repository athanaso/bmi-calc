<?php // no direct access
/**
 * @Author		I am Bored So I Blog
 * @version		1.0
 * @copyright	Theo van der Sluijs / IAMBOREDSOIBLOG.eu
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * 
 * We ask a download fee of 1 euro.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<!-- DONT EDIT THE FOLLOWING JAVASCRIPT LINES  -->
<script type="text/javascript">
//var useCm = true; 	// Using centimetre for height, false = inch
//var useKg = true;	// Using kilos for weight, false = pounds
var graphColors = ['#6600CC','#66CC00','#00CCCC','#CC0000'];

var graphLabels = ['< 18.5: <?php echo JText::_('UNDERWEIGHT'); ?>','18.5 <> 24.9: <?php echo JText::_('NORMAL'); ?>','25.0 <> 29.9: <?php echo JText::_('OVERWEIGHT'); ?>','30 >: <?php echo JText::_('OBESE'); ?>'];
var labelsPerRow = 1;	/* Help labels above graph */
var barHeight = 300; 	// Total height of bar
var barWidth = 50;		// Width of bars */

// Don't change anything below this point */

var calculatorObj;
var calculatorGraphObj;
var bmiArray = [0,18.5,25,30,60];	/* BMI VALUES */
var weightDiv = false;

function gObj(obj) {
var theObj;
if(document.all){
	if(typeof obj=="string"){
		return document.all(obj);
	}else{
		return obj.style;
	}
}
if(document.getElementById){
	if(typeof obj=="string"){
		return document.getElementById(obj);
	}else{
		return obj.style;
	}
}
return null;
}
function trimAll(sString){
while (sString.substring(0,1) == " "){
	sString = sString.substring(1, sString.length);
}
while (sString.substring(sString.length-1, sString.length) == " "){
	sString = sString.substring(0,sString.length-1);
}
return sString;
}
function isNumber(val){
val=val+"";
if (val.length<1) return false;
if (isNaN(val)){
	return false;
}else{
	return true;
}
}
function formatNum(inNum){
outStr = ""+inNum;
inNum = parseFloat(outStr);
if ((outStr.length)>10){
	outStr = "" + inNum.toPrecision(10);
}
if (outStr.indexOf(".")>-1){
	while (outStr.charAt(outStr.length-1) == "0"){
		outStr = outStr.substr(0,(outStr.length-1));
	}
	if (outStr.charAt(outStr.length-1) == ".") outStr = outStr.substr(0,(outStr.length-1));
	return outStr;
}else{
	return outStr;
}
}
function showquickmsg(inStr, isError){
if (isError) inStr = "<font color=red>" + inStr + "</font>";
gObj("bmicoutput").innerHTML = inStr;
}

var girlA = new Array();
girlA[0] = new Array(0,0,0);
girlA[1] = new Array(0,0,0);
girlA[2] = new Array(14.4, 18, 19.1);
girlA[3] = new Array(14, 17.2, 18.3);
girlA[4] = new Array( 13.7, 16.8, 18);
girlA[5] = new Array( 13.5, 16.8, 18.3);
girlA[6] = new Array( 13.4, 17.1, 18.8);
girlA[7] = new Array( 13.4, 17.6, 19.6);
girlA[8] = new Array( 13.5, 18.3, 20.7);
girlA[9] = new Array( 13.7, 19.1, 21.8);
girlA[10] = new Array( 14, 19.9, 22.9);
girlA[11] = new Array( 14.4, 20.8, 24.1);
girlA[12] = new Array( 14.8, 21.7, 25.2);
girlA[13] = new Array( 15.3, 22.5, 26.5);
girlA[14] = new Array( 15.8, 23.5, 27.2);
girlA[15] = new Array( 16.3, 24, 28.1);
girlA[16] = new Array( 16.8, 24.7, 28.9);
girlA[17] = new Array( 17.2, 25.2, 29.6);
girlA[18] = new Array( 17.5, 25.7, 30.3);
girlA[19] = new Array( 17.8, 26.1, 31);
girlA[20] = new Array( 17.8, 26.5, 31.8);

var boyA = new Array();
boyA[0] = new Array(0,0,0);
boyA[1] = new Array(0,0,0);
boyA[2] = new Array(14.7, 18.2, 19.3);
boyA[3] = new Array( 14.4, 17.4, 18.3);
boyA[4] = new Array( 14, 16.9, 17.8);
boyA[5] = new Array( 13.8, 16.8, 17.9);
boyA[6] = new Array( 13.7, 17, 18.4);
boyA[7] = new Array( 13.7, 17.4, 19.1);
boyA[8] = new Array( 13.8, 17.9, 20);
boyA[9] = new Array( 14, 18.6, 21.1);
boyA[10] = new Array( 14.2, 19.4, 22.1);
boyA[11] = new Array( 14.5, 20.2, 23.2);
boyA[12] = new Array( 15, 21, 24.2);
boyA[13] = new Array( 15.5, 21.8, 25.1);
boyA[14] = new Array( 16, 22.6, 26);
boyA[15] = new Array( 16.5, 23.4, 26.8);
boyA[16] = new Array( 17.1, 24.2, 27.5);
boyA[17] = new Array( 17.7, 24.9, 28.2);
boyA[18] = new Array( 18.2, 25.6, 28.9);
boyA[19] = new Array( 18.7, 26.3, 29.7);
boyA[20] = new Array( 19.1, 27, 30.6);

function showCalc(inval){
	if (inval == 2){
		var useCm = true; 	// Using centimetre for height, false = inch
		var useKg = true;
		gObj("standardheightweight").style.display = "none";
		gObj("metricheightweight").style.display = "block";
	}else{
		var useCm = false; 	// Using centimetre for height, false = inch
		var useKg = false;
		gObj("standardheightweight").style.display = "block";
		gObj("metricheightweight").style.display = "none";
	}
}

function getTheWeight(bmiNum, heightNum, weightUnit){
outPutNum = 0;
if (weightUnit == "kg"){
	outPutNum = bmiNum * heightNum * heightNum / 10000;
	outPutNum = outPutNum.toFixed(1);
}else{
	outPutNum = bmiNum * heightNum * heightNum / 4535.92;
	outPutNum = outPutNum.toFixed(1);
}
return outPutNum;
}

function bmicalc(){


showquickmsg("calculating...",true);
cage = gObj("cage").value;

cheightfeet = gObj("cheightfeet").value;
cheightinch = gObj("cheightinch").value;
cpound = gObj("cpound").value;
cheightmeter = gObj("cheightmeter").value;
ckg = gObj("ckg").value;
ctype = "standard";

if (!(gObj("ctype1").checked)){
	ctype = "metric";
}
ismale=false;
if (gObj("csex1").checked){
	ismale = true;
}


if (!isNumber(cage) || (cage.length<1)){
	showquickmsg("<?php echo JText::_('age need to be numeric'); ?>",true);
	return;
}else{
	if ((cage < 2) || (cage > 120)){
		showquickmsg("<?php echo JText::_('age need to be between 2 and 120'); ?>",true);
		return;
	}
}

if (ctype=="standard"){
	cheightfeet = (cheightfeet*1);
	if ((!isNumber(cheightfeet)) || (!isNumber(cheightinch)) || (cheightfeet.length<1) || (cheightinch.length<1)){
		showquickmsg("<?php echo JText::_('height need to be numeric'); ?>",true);
		return;
	}else if (!isNumber(cpound) || (cpound.length<1)){
		showquickmsg("<?php echo JText::_('weight need to be numeric'); ?>",true);
		return;
	}

	cheightmeter = 30.48 * parseFloat(cheightfeet) + 2.54 * parseFloat(cheightinch);
	ckg = parseFloat(cpound) * 0.453592;
}else{
	if (!isNumber(cheightmeter) || (cheightmeter.length<1)){
		showquickmsg("<?php echo JText::_('height need to be numeric'); ?>",true);
		return;
	}else if (!isNumber(ckg) || (ckg.length<1)){
		showquickmsg("<?php echo JText::_('weight need to be numeric'); ?>",true);
		return;
	}
	ckg=parseFloat(ckg);
	cheightmeter=parseFloat(cheightmeter);
}

cage=parseFloat(cage);

cbmi = 10000*ckg/cheightmeter/cheightmeter;
cbmi = parseFloat(formatNum(cbmi)).toFixed(2);

<?php
if($graph==1){echo "createWeightBar(cbmi);";}
?>

outPutStr = "<?php echo JText::_('BMI'); ?> = " + cbmi + " kg/m<sup>2</sup> &nbsp; (";
if (cage > 20){
	if (cbmi<16.5){
		outPutStr += "<font color=red><b><?php echo JText::_('severely underweight'); ?></b></font>";
	}else if(cbmi<18.5){
		outPutStr += "<font color=#FDD790><b><?php echo JText::_('Underweight'); ?></b></font>";
	}else if(cbmi<25){
		outPutStr += "<font color=green><b><?php echo JText::_('Normal'); ?></b></font>";
	}else if(cbmi<30){
		outPutStr += "<font color=#FDD790><b><?php echo JText::_('Overweight'); ?></b></font>";
	}else if(cbmi<35){
		outPutStr += "<font color=#F69D92><b><?php echo JText::_('Obese Class I'); ?></b></font>";
	}else if(cbmi<40){
		outPutStr += "<font color=#F05340><b><?php echo JText::_('Obese Class II'); ?></b></font>";
	}else{
		outPutStr += "<font color=red><b><?php echo JText::_('Obese Class III'); ?></b></font>";
	}
	outPutStr += ")";
	outPutStr += "<br /><?php echo JText::_('normal BMI range'); ?>: 18.5 - 25 kg/m<sup>2</sup>";
	if (ctype=="standard"){
		outPutStr += "<br /><?php echo JText::_('normal weight range for the height'); ?>: " + getTheWeight(18.5, cheightmeter, "lb") + " - " + getTheWeight(25, cheightmeter, "lb") + " lbs";
	}else{
		outPutStr += "<br /><?php echo JText::_('normal weight range for the height'); ?>: " + getTheWeight(18.5, cheightmeter, "kg") + " - " + getTheWeight(25, cheightmeter, "kg") + " kgs";
	}

}else{
	line5 = 0;
	line85 = 0;
	line95 = 0;
	if (ismale){
		line5 = boyA[cage][0];
		line85 = boyA[cage][1];
		line95 = boyA[cage][2];
	}else{
		line5 = girlA[cage][0];
		line85 = girlA[cage][1];
		line95 = girlA[cage][2];
	}

	if (cbmi<line5){
		outPutStr += "<font color=red><b><?php echo JText::_('UNDERWEIGHT'); ?></b></font>";
	}else if(cbmi<line85){
		outPutStr += "<font color=green><b><?php echo JText::_('HEALTHY WEIGHT'); ?></b></font>";
	}else if(cbmi<line95){
		outPutStr += "<font color=#F69D92><?php echo JText::_('AT RISK OF OVERWEIGHT'); ?></b></font>";
	}else{
		outPutStr += "<font color=red><b><?php echo JText::_('OVERWEIGHT'); ?></b></font>";
	}
	outPutStr += ")";
	outPutStr += "<br /><?php echo JText::_('NORMAL BMI RANGE'); ?>: " + line5 + " - " + line85 + " kg/m<sup>2</sup>";

	if (ctype=="standard"){
		outPutStr += "<br /><?php echo JText::_('NORMAL WEIGHT RANGE FOR THE HEIGHT'); ?>: " + getTheWeight(line5, cheightmeter, "<?php echo JText::_('lb'); ?>") + " - " + getTheWeight(line85, cheightmeter, "<?php echo JText::_('lb'); ?>") + " <?php echo JText::_('lbs'); ?>";
	}else{
		outPutStr += "<br /><?php echo JText::_('NORMAL WEIGHT RANGE FOR THE HEIGHT'); ?>: " + getTheWeight(line5, cheightmeter, "kg") + " - " + getTheWeight(line85, cheightmeter, "kg") + " kgs";
	}
}
showquickmsg(outPutStr, false);
}

</script>
<!-- DONT EDIT THE ABOVE JAVASCRIPT LINES  -->

<div style="margin-top:5px;"> 
  <form>
    <div id="calinputtable">
      <div class="rowCalc" <?php if($unit == 'u'){echo "style='display:block;'" ;}else{echo "style='display:none;'";}?> >
        <div class="leftColCalc"> <?php echo JText::_('unit'); ?> </div>
        <div class="middleColCalc">
          <label for="ctype1">
            <input type="radio" name="ctype" id="ctype1" value="standard" onclick="showCalc(1)" <?php if($unit == 'standard' ||$unit == 'u'){echo "checked";}?> />
            <?php echo JText::_('us'); ?></label>
          <label for="ctype2">
            <input type="radio" name="ctype" id="ctype2" value="metric" onclick="showCalc(2)" <?php if($unit == 'metric'){echo "checked";}?>/>
            <?php echo JText::_('Metric'); ?></label>
        </div>
      </div>

      <div class="rowCalc">
        <div class="leftColCalc"> <?php echo JText::_('age'); ?> </div>
        <div class="middleColCalc">
          <input type="text" name="cage" size="6" id="cage" value="" style="text-align: right;">
        </div>
      </div>
      <div class="rowCalc" <?php if($sex == 'u'){echo "style='display:block;'";}else{echo "style='display:none;'";}?>>
        <div class="leftColCalc"> <?php echo JText::_('sex'); ?> </div>
        <div class="middleColCalc">
          <label for="csex1">
            <input type="radio" name="csex" id="csex1" value="m" onclick="bmicalc()" <?php if($sex == 'm' || $sex == 'u'){echo "checked";} ?> />
            <?php echo JText::_('male'); ?></label>
          <label for="csex2">
            <input type="radio" name="csex" id="csex2" value="f" onclick="bmicalc()" <?php if($sex == 'f'){echo "checked";}?>/>
            <?php echo JText::_('female'); ?></label>
        </div>
      </div>
    </div>

    <div id="standardheightweight" >
      <div class="rowCalc">
        <div class="leftColCalc"> <?php echo JText::_('height'); ?> </div>
        <div class="middleColCalc">
          <input type="text" name="cheightfeet" size="1" id="cheightfeet" value="" style="text-align: right;">
          <?php echo JText::_('ft'); ?>
          <input type="text" name="cheightinch" size="1" id="cheightinch" value="" style="text-align: right;">
        </div>
        <div class="rightColCalc"> <?php echo JText::_('in'); ?> </div>
      </div>
      <div class="rowCalc">
        <div class="leftColCalc"> <?php echo JText::_('weight'); ?> </div>
        <div class="middleColCalc">
          <input type="text" name="cpound" size="4" id="cpound" value="" style="text-align: right;">
        </div>
        <div class="rightColCalc"> <?php echo JText::_('lb'); ?> </div>
      </div>
    </div>

    <div id="metricheightweight">
      <div class="rowCalc">
        <div class="leftColCalc"> <?php echo JText::_('height'); ?> </div>
        <div class="middleColCalc">
          <input type="text" name="cheightmeter" size="4" id="cheightmeter" value="" style="text-align: right;">
        </div>
        <div class="rightColCalc"> <?php echo JText::_('cm'); ?> </div>
      </div>
      <div class="rowCalc" id="metricweight">
        <div class="leftColCalc"> <?php echo JText::_('weight'); ?> </div>
        <div class="middleColCalc">
          <input type="text" name="ckg" size="4" id="ckg" value="" style="text-align: right;">
        </div>
        <div class="rightColCalc"> <?php echo JText::_('kg'); ?> </div>
      </div>
    </div>

    <div class="btnCalc">
      <input type="button" value="<?php echo JText::_('Calculate'); ?>" onclick="bmicalc()">
    </div>
  </form>
  <div id="bmicoutput"></div>
  <?php
		if($unit == 'standard'){ $showCalc="1";}
		elseif($unit == 'metric'){ $showCalc="2";}
		else{ $showCalc="1";}
  ?>
</div>

	<script type="text/javascript">
	
	function createWeightBar(inputValue){
		
		if(!weightDiv){
			self.status = Math.random();
			weightDiv = document.createElement('DIV');
			weightDiv.style.width = barWidth + 'px';
			weightDiv.className='barContainer';
			weightDiv.style.left = Math.round((calculatorGraphObj.offsetWidth/2) + ((calculatorGraphObj.offsetWidth/2) /2) - (barWidth/2)) + 'px';
			calculatorGraphObj.appendChild(weightDiv);
			var span = document.createElement('SPAN');
			weightDiv.appendChild(span);
			
			var innerSpan = document.createElement('SPAN');
			innerSpan.className='labelSpan';
			span.appendChild(innerSpan);			
		}else{
			span = weightDiv.getElementsByTagName('SPAN')[0];
			innerSpan = weightDiv.getElementsByTagName('SPAN')[1];
		}
		var color = graphColors[graphColors.length-1];
		for(var no = bmiArray.length-1;no>0;no--){
			if(bmiArray[no]>inputValue)weightDiv.style.backgroundColor = graphColors[no-1];
		}
		if(inputValue/1>1){
			innerSpan.innerHTML = inputValue; 
			span.style.display='inline';
		}else span.style.display='none';
		var height = Math.min(Math.round(barHeight * (inputValue / bmiArray[bmiArray.length-1])),barHeight-10);
		span.style.lineHeight = Math.round(height) + 'px';
		weightDiv.style.height = height + 'px';
		
	}
	
	
	function validateField()
	{
		this.value = this.value.replace(/[^0-9,\.]/g,'');
		
	}
	
	function initBmiCalculator()
	{
		calculatorObj = document.getElementById('bmi_calculator');	
		calculatorGraphObj = document.getElementById('bmi_calculator_graph');	
				
		var labelDiv = document.createElement('DIV');
		labelDiv.className = 'graphLabels';
		calculatorGraphObj.appendChild(labelDiv);
		for(var no=graphLabels.length-1;no>=0;no--){
			var colorDiv = document.createElement('DIV');
			colorDiv.className='square';
			colorDiv.style.backgroundColor = graphColors[no];
			colorDiv.innerHTML = '<span></span>';
			labelDiv.appendChild(colorDiv);
			
			var labelDivTxt = document.createElement('DIV');
			labelDivTxt.innerHTML = graphLabels[no];
			labelDiv.appendChild(labelDivTxt);
			labelDivTxt.className='label';
			
			if((no+1)%labelsPerRow==0){
				var clearDiv = document.createElement('DIV');
				clearDiv.className='clear';
				labelDiv.appendChild(clearDiv);				
			}		
		}
		var clearDiv = document.createElement('DIV');
		clearDiv.className='clear';
		labelDiv.appendChild(clearDiv);	
						
		var graphDiv = document.createElement('DIV');
		graphDiv.className='barContainer';
		graphDiv.style.width = barWidth + 'px';
		graphDiv.style.left = Math.round(((calculatorGraphObj.offsetWidth/2) /2) - (barWidth/2)) + 'px';
		graphDiv.style.height = barHeight;
		calculatorGraphObj.appendChild(graphDiv);
		
		var totalHeight = 0;
		for(var no=bmiArray.length-1;no>0;no--){
			var aDiv = document.createElement('DIV');
			aDiv.style.backgroundColor = graphColors[no-1];
			aDiv.innerHTML = '<span></span>';
			var height = Math.round(barHeight * (bmiArray[no] - bmiArray[no-1]) / bmiArray[bmiArray.length-1]) - 1;
			aDiv.style.height = height + 'px';
			graphDiv.appendChild(aDiv);	
			
		}		
		
		createWeightBar(1);
	}
	
	</script>
<?php
if($graph==1){
?>
	<div id="bmi_calculator">
		<div class="calculator_graph" id="bmi_calculator_graph"></div>
	</div>

<?php
}
?>  
  
  <script type="text/javascript">showCalc(<?php echo $showCalc;?>);</script>

<?php
if($graph==1){
?>
  <script type="text/javascript">initBmiCalculator();</script>
<?php
}
?>
  

<?php
if($Blink == 0){
	echo "<div style='font-size:10px; text-align: center;margin-top:10px;'>";
	echo "Created by <a href='http://www.iamboredsoiblog.eu' target='_blank'>IamBoredSoIBlog.eu</a>";
	echo "</div>";
}?>