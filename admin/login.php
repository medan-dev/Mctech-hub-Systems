<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include '../includes/config.php';
if (isset($_GET['logout'])) {
    session_destroy(); session_start();
    $_SESSION['logout_success'] = 'Logged out successfully.';
    header('Location: login.php'); exit;
}
if (isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
$error = ''; $success = $_SESSION['logout_success'] ?? '';
unset($_SESSION['logout_success']);
$stats = ['projects'=>0,'clients'=>0,'services'=>0];
try {
    $stats['projects'] = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $stats['clients']  = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $stats['services'] = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
} catch(Exception $e){}
if ($_POST) {
    $username = trim($_POST['username']??''); $password = $_POST['password']??'';
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username=? AND is_active=1");
    $stmt->execute([$username]); $admin = $stmt->fetch();
    if ($admin && password_verify($password,$admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: index.php'); exit;
    } else { $error = 'Invalid username or password.'; }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — Mctech-hub Systems</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="icon" href="../assets/images/logo.png">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#020817;color:#e2e8f0;-webkit-font-smoothing:antialiased}
#c{position:fixed;inset:0;z-index:0}

/* ── SHELL ── */
.shell{position:relative;z-index:10;display:grid;grid-template-columns:1fr 460px;height:100vh}

/* ── LEFT PANEL ── */
.lp{display:flex;flex-direction:column;justify-content:center;padding:3rem 4rem;position:relative}
.lp::after{content:'';position:absolute;top:8%;bottom:8%;right:0;width:1px;background:linear-gradient(to bottom,transparent,rgba(255,255,255,.07) 30%,rgba(255,255,255,.07) 70%,transparent)}
.badge{display:inline-flex;align-items:center;gap:7px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:30px;padding:5px 14px;font-size:.67rem;font-weight:700;color:rgba(255,255,255,.45);text-transform:uppercase;letter-spacing:2px;margin-bottom:1.5rem;width:fit-content}
.badge .dot{width:6px;height:6px;border-radius:50%;background:#10b981;box-shadow:0 0 8px #10b981;animation:blink 2s ease-in-out infinite}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}
.brand{display:flex;align-items:center;gap:.8rem;margin-bottom:2rem}
.brand img{width:50px;height:50px;border-radius:13px;border:1px solid rgba(255,255,255,.1);filter:drop-shadow(0 0 18px rgba(120,140,255,.45))}
.brand-name{font-size:1.1rem;font-weight:800;color:#fff}
.brand-sub{font-size:.68rem;color:rgba(255,255,255,.35);font-weight:500}
.headline{font-size:clamp(2.1rem,3vw,3.2rem);font-weight:800;line-height:1.08;letter-spacing:-1.2px;color:#fff;margin-bottom:.9rem}
.headline span{background:linear-gradient(135deg,#a78bfa,#60a5fa,#f472b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.desc{font-size:.88rem;color:rgba(255,255,255,.38);line-height:1.75;max-width:380px;margin-bottom:2.25rem}
.stats{display:flex;gap:1.75rem;margin-bottom:2.5rem}
.stat .n{font-size:2.1rem;font-weight:800;color:#fff;line-height:1}
.stat .l{font-size:.65rem;color:rgba(255,255,255,.35);font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-top:3px}
.pills{display:flex;flex-wrap:wrap;gap:8px}
.pill{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:20px;padding:5px 13px;font-size:.71rem;font-weight:600;color:rgba(255,255,255,.45);transition:.2s}
.pill:hover{background:rgba(255,255,255,.09);color:rgba(255,255,255,.8)}
.pill.ac{background:rgba(167,139,250,.12);border-color:rgba(167,139,250,.3);color:#c4b5fd}

/* ── RIGHT FORM PANEL ── */
.fp{display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem}
.card{width:100%;max-width:400px;background:rgba(2,6,20,.82);border:1px solid rgba(255,255,255,.09);border-radius:28px;padding:2.5rem 2.25rem;backdrop-filter:blur(32px);-webkit-backdrop-filter:blur(32px);box-shadow:0 0 0 1px rgba(255,255,255,.04) inset,0 32px 80px rgba(0,0,0,.6),0 0 100px rgba(120,90,255,.1);position:relative;overflow:hidden}
.card::before{content:'';position:absolute;top:0;left:10%;right:10%;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,.3),transparent)}
.card-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#7c3aed,#60a5fa);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;margin-bottom:1.25rem;box-shadow:0 8px 24px rgba(124,58,237,.4)}
.card h1{font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:5px;letter-spacing:-.4px}
.card .sub{font-size:.78rem;color:rgba(255,255,255,.35);margin-bottom:1.75rem}
.alert{display:flex;align-items:center;gap:8px;border-radius:12px;padding:.72rem 1rem;font-size:.77rem;font-weight:500;margin-bottom:1.2rem;border:1px solid}
.alert.e{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#fca5a5}
.alert.s{background:rgba(16,185,129,.1);border-color:rgba(16,185,129,.3);color:#6ee7b7}
.fg{margin-bottom:1rem}
.lbl{display:block;font-size:.68rem;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.iw{position:relative}
.ii{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:.78rem;color:rgba(255,255,255,.25);pointer-events:none;z-index:2;transition:color .2s}
.inp{width:100%;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.09);border-radius:12px;padding:.72rem 2.5rem;font-family:inherit;font-size:.84rem;color:#fff;outline:none;transition:all .2s;backdrop-filter:blur(4px)}
.inp::placeholder{color:rgba(255,255,255,.2)}
.inp:focus{border-color:#7c3aed;background:rgba(124,58,237,.07);box-shadow:0 0 0 4px rgba(124,58,237,.13),0 0 24px rgba(124,58,237,.08)}
.iw:focus-within .ii{color:#a78bfa}
.pw-tog{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.25);cursor:pointer;font-size:.8rem;z-index:2;padding:0;transition:color .2s}
.pw-tog:hover{color:rgba(255,255,255,.65)}
.btn-login{width:100%;margin-top:.4rem;padding:.85rem;border:none;border-radius:13px;background:linear-gradient(135deg,#7c3aed 0%,#4361ee 60%,#60a5fa 100%);color:#fff;font-family:inherit;font-size:.87rem;font-weight:700;letter-spacing:.3px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .3s;position:relative;overflow:hidden;box-shadow:0 8px 28px rgba(124,58,237,.4)}
.btn-login::before{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:linear-gradient(135deg,transparent 40%,rgba(255,255,255,.15) 50%,transparent 60%);transform:rotate(45deg) translateX(-120%);transition:transform .6s ease}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 14px 38px rgba(124,58,237,.55)}
.btn-login:hover::before{transform:rotate(45deg) translateX(120%)}
.btn-login:disabled{opacity:.6;cursor:not-allowed;transform:none}
.foot{margin-top:1.75rem;text-align:center;font-size:.67rem;color:rgba(255,255,255,.22);line-height:1.7}
.foot a{color:rgba(255,255,255,.38);text-decoration:none}
.foot a:hover{color:rgba(255,255,255,.7)}

/* Warp overlay */
#warp-overlay{position:fixed;inset:0;z-index:20;background:radial-gradient(ellipse at center,rgba(120,90,255,.0) 0%,rgba(0,0,0,0) 100%);pointer-events:none;transition:background 1.2s}
#warp-overlay.active{background:radial-gradient(ellipse at center,#fff 0%,rgba(120,90,255,.9) 40%,rgba(5,0,30,.95) 100%)}

@media(max-width:900px){.shell{grid-template-columns:1fr}.lp{display:none}.fp{min-height:100vh}body{overflow:auto}}
</style>
</head>
<body>
<canvas id="c"></canvas>
<div id="warp-overlay"></div>

<div class="shell">
  <!-- LEFT -->
  <div class="lp">
    <div class="badge"><span class="dot"></span>Admin Portal</div>
    <div class="brand">
      <img src="../assets/images/logo.png" alt="logo">
      <div><div class="brand-name">Mctech-hub Systems</div><div class="brand-sub">Uganda · Africa · Global</div></div>
    </div>
    <h2 class="headline">Your Digital<br><span>Command&nbsp;Centre</span></h2>
    <p class="desc">Manage services, leads, projects, blog and analytics from one powerful interstellar dashboard.</p>
    <div class="stats">
      <div class="stat"><div class="n"><?php echo $stats['projects'];?>+</div><div class="l">Projects</div></div>
      <div class="stat"><div class="n"><?php echo $stats['clients'];?>+</div><div class="l">Leads</div></div>
      <div class="stat"><div class="n"><?php echo $stats['services'];?>+</div><div class="l">Services</div></div>
    </div>
    <div class="pills">
      <span class="pill"><i class="fas fa-chart-line"></i>Analytics</span>
      <span class="pill"><i class="fas fa-envelope"></i>Lead Mgmt</span>
      <span class="pill ac"><i class="fas fa-rocket"></i>CMS</span>
      <span class="pill"><i class="fas fa-users"></i>Subscribers</span>
      <span class="pill"><i class="fas fa-broadcast-tower"></i>Broadcast</span>
      <span class="pill"><i class="fas fa-star"></i>Reviews</span>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="fp">
    <div class="card">
      <div class="card-icon"><i class="fas fa-terminal"></i></div>
      <h1>Welcome back 👋</h1>
      <p class="sub">Sign in to your admin control panel.</p>
      <?php if($success):?><div class="alert s"><i class="fas fa-check-circle"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
      <?php if($error):?><div class="alert e"><i class="fas fa-exclamation-circle"></i><?php echo htmlspecialchars($error);?></div><?php endif;?>
      <form method="POST" id="loginForm" autocomplete="off">
        <div class="fg">
          <label class="lbl">Username</label>
          <div class="iw">
            <i class="fas fa-user ii"></i>
            <input type="text" name="username" class="inp" placeholder="Enter username" required autofocus value="<?php echo htmlspecialchars($_POST['username']??'');?>">
          </div>
        </div>
        <div class="fg">
          <label class="lbl">Password</label>
          <div class="iw">
            <i class="fas fa-lock ii"></i>
            <input type="password" name="password" id="pwF" class="inp" placeholder="••••••••••" required style="padding-right:2.8rem">
            <button type="button" class="pw-tog" onclick="togglePw()"><i class="fas fa-eye" id="pwI"></i></button>
          </div>
        </div>
        <button type="submit" class="btn-login" id="loginBtn">
          <i class="fas fa-sign-in-alt"></i><span id="btnTxt">Sign In to Dashboard</span>
        </button>
      </form>
      <div class="foot">
        <i class="fas fa-shield-alt" style="margin-right:4px"></i>Protected portal · Mctech-hub Systems &copy; <?php echo date('Y');?><br>
        <a href="../index.php">← Back to website</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js"></script>
<script>
/* ════════════════════════════════════════
   CINEMATIC 3D GALAXY — Three.js
════════════════════════════════════════ */
const canvas   = document.getElementById('c');
const renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:false});
renderer.setPixelRatio(Math.min(devicePixelRatio,2));
renderer.setSize(innerWidth, innerHeight);
renderer.setClearColor(0x020817,1);

const scene  = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(60, innerWidth/innerHeight, 0.1, 2000);
camera.position.set(0, 8, 18);
camera.lookAt(0,0,0);

// ── Star glow texture ──
function makeStarTex() {
    const cv = document.createElement('canvas'); cv.width = cv.height = 128;
    const ctx = cv.getContext('2d');
    const g   = ctx.createRadialGradient(64,64,0,64,64,64);
    g.addColorStop(0,   'rgba(255,255,255,1)');
    g.addColorStop(0.12,'rgba(200,215,255,0.9)');
    g.addColorStop(0.4, 'rgba(100,140,255,0.35)');
    g.addColorStop(1,   'rgba(0,0,0,0)');
    ctx.fillStyle = g; ctx.fillRect(0,0,128,128);
    return new THREE.CanvasTexture(cv);
}
const starTex = makeStarTex();

// ── Galaxy spiral arms (100k stars) ──
function makeGalaxy() {
    const COUNT = 100000, BRANCHES = 3, SPIN = 1.4, RADIUS = 12;
    const pos = new Float32Array(COUNT*3);
    const col = new Float32Array(COUNT*3);
    const cA = new THREE.Color('#ffffff');
    const cB = new THREE.Color('#60a5fa');
    const cC = new THREE.Color('#a78bfa');
    const cD = new THREE.Color('#f472b6');

    for(let i=0;i<COUNT;i++){
        const i3  = i*3;
        const r   = Math.random()*RADIUS;
        const spin= r*SPIN;
        const br  = (i%BRANCHES)/BRANCHES * Math.PI*2;
        const rnd = Math.pow(Math.random(), 2.8);
        const rx  = (Math.random()-.5)*rnd*r*0.5;
        const ry  = (Math.random()-.5)*rnd*r*0.09;
        const rz  = (Math.random()-.5)*rnd*r*0.5;
        pos[i3]   = Math.cos(br+spin)*r + rx;
        pos[i3+1] = ry + (Math.random()-.5)*0.25;
        pos[i3+2] = Math.sin(br+spin)*r + rz;

        const t = r/RADIUS;
        let c;
        if(t<0.1)       c = cA.clone().lerp(cB, t/0.1);
        else if(t<0.45) c = cB.clone().lerp(cC, (t-.1)/.35);
        else if(t<0.75) c = cC.clone().lerp(cD, (t-.45)/.3);
        else            c = cD.clone();
        c.r = Math.min(1, c.r+(Math.random()-.5)*.12);
        c.g = Math.min(1, c.g+(Math.random()-.5)*.1);
        c.b = Math.min(1, c.b+(Math.random()-.5)*.1);
        col[i3]=c.r; col[i3+1]=c.g; col[i3+2]=c.b;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos,3));
    geo.setAttribute('color',    new THREE.BufferAttribute(col,3));
    return new THREE.Points(geo, new THREE.PointsMaterial({
        size:0.09, sizeAttenuation:true, depthWrite:false,
        blending:THREE.AdditiveBlending, vertexColors:true,
        map:starTex, transparent:true
    }));
}

// ── Bright core ──
function makeCore() {
    const COUNT = 3000;
    const pos = new Float32Array(COUNT*3);
    const col = new Float32Array(COUNT*3);
    for(let i=0;i<COUNT;i++){
        const i3=i*3, r=Math.random()*1.8;
        const th=Math.random()*Math.PI*2, ph=Math.acos(2*Math.random()-1);
        pos[i3]=r*Math.sin(ph)*Math.cos(th);
        pos[i3+1]=r*Math.cos(ph)*.25;
        pos[i3+2]=r*Math.sin(ph)*Math.sin(th);
        col[i3]=1; col[i3+1]=0.9+Math.random()*.1; col[i3+2]=1;
    }
    const geo=new THREE.BufferGeometry();
    geo.setAttribute('position',new THREE.BufferAttribute(pos,3));
    geo.setAttribute('color',new THREE.BufferAttribute(col,3));
    return new THREE.Points(geo, new THREE.PointsMaterial({
        size:.25, sizeAttenuation:true, depthWrite:false,
        blending:THREE.AdditiveBlending, vertexColors:true,
        map:starTex, transparent:true
    }));
}

// ── Halo (background star field) ──
function makeHalo() {
    const COUNT=25000;
    const pos=new Float32Array(COUNT*3);
    const col=new Float32Array(COUNT*3);
    for(let i=0;i<COUNT;i++){
        const i3=i*3, th=Math.random()*Math.PI*2, ph=Math.acos(2*Math.random()-1);
        const r=14+Math.random()*90;
        pos[i3]=r*Math.sin(ph)*Math.cos(th);
        pos[i3+1]=r*Math.cos(ph)*.35;
        pos[i3+2]=r*Math.sin(ph)*Math.sin(th);
        const b=.3+Math.random()*.7;
        const warm=Math.random()<.15;
        col[i3]=warm?b:b*.75; col[i3+1]=warm?b*.7:b*.85; col[i3+2]=warm?b*.35:b;
    }
    const geo=new THREE.BufferGeometry();
    geo.setAttribute('position',new THREE.BufferAttribute(pos,3));
    geo.setAttribute('color',new THREE.BufferAttribute(col,3));
    return new THREE.Points(geo, new THREE.PointsMaterial({
        size:.055, sizeAttenuation:true, depthWrite:false,
        blending:THREE.AdditiveBlending, vertexColors:true,
        map:starTex, transparent:true
    }));
}

// ── Nebula clouds ──
function makeNebula() {
    const defs=[
        {n:4000,cx:-5,cy:1.5,cz:3,  hex:'#e879f9',spread:4.5},
        {n:3500,cx:6, cy:-.5,cz:-4, hex:'#60a5fa',spread:4},
        {n:3000,cx:-3,cy:.5, cz:-6, hex:'#a78bfa',spread:3.5},
        {n:2000,cx:3, cy:1,  cz:6,  hex:'#fb923c',spread:3},
        {n:1500,cx:0, cy:2,  cz:0,  hex:'#e879f9',spread:2},
    ];
    const grp=new THREE.Group();
    for(const d of defs){
        const pos=new Float32Array(d.n*3), col=new Float32Array(d.n*3);
        const c=new THREE.Color(d.hex);
        for(let i=0;i<d.n;i++){
            const i3=i*3, th=Math.random()*Math.PI*2, ph=Math.acos(2*Math.random()-1);
            const r=Math.random()*d.spread;
            pos[i3]=d.cx+r*Math.sin(ph)*Math.cos(th);
            pos[i3+1]=d.cy+r*Math.cos(ph)*.5;
            pos[i3+2]=d.cz+r*Math.sin(ph)*Math.sin(th);
            const f=Math.random()*.4+.05;
            col[i3]=c.r*f; col[i3+1]=c.g*f; col[i3+2]=c.b*f;
        }
        const geo=new THREE.BufferGeometry();
        geo.setAttribute('position',new THREE.BufferAttribute(pos,3));
        geo.setAttribute('color',new THREE.BufferAttribute(col,3));
        grp.add(new THREE.Points(geo, new THREE.PointsMaterial({
            size:.8, sizeAttenuation:true, depthWrite:false,
            blending:THREE.AdditiveBlending, vertexColors:true, transparent:true, opacity:.55
        })));
    }
    return grp;
}

const galaxy = makeGalaxy();
const core   = makeCore();
const halo   = makeHalo();
const nebula = makeNebula();
scene.add(galaxy, core, halo, nebula);

// ── Camera orbit + animate ──
let camAngle = 0, warping = false, warpT = 0;
window.addEventListener('resize',()=>{
    camera.aspect=innerWidth/innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(innerWidth,innerHeight);
});

// Subtle card parallax on mouse
const card = document.querySelector('.card');
document.addEventListener('mousemove', e=>{
    if(!card) return;
    const dx=(e.clientX-innerWidth/2)/innerWidth;
    const dy=(e.clientY-innerHeight/2)/innerHeight;
    card.style.transform=`perspective(900px) rotateX(${-dy*4}deg) rotateY(${dx*4}deg)`;
});

function tick(){
    requestAnimationFrame(tick);
    camAngle += .00035;
    if(!warping){
        camera.position.x = Math.cos(camAngle)*18;
        camera.position.z = Math.sin(camAngle)*18;
        camera.position.y = Math.sin(camAngle*.38)*5 + 5;
        camera.lookAt(0,0,0);
        galaxy.rotation.y += .00075;
        nebula.rotation.y += .00045;
        core.rotation.y   += .0012;
    } else {
        warpT = Math.min(warpT+.04, 3);
        camera.position.z -= warpT*warpT*.8;
        camera.fov = Math.min(camera.fov + warpT*2.5, 140);
        camera.updateProjectionMatrix();
        galaxy.rotation.y += .006 + warpT*.008;
    }
    renderer.render(scene,camera);
}
tick();

// ── Login interactions ──
function togglePw(){
    const f=document.getElementById('pwF'), i=document.getElementById('pwI');
    const isP=f.type==='password';
    f.type=isP?'text':'password';
    i.className=isP?'fas fa-eye-slash':'fas fa-eye';
}

document.getElementById('loginForm').addEventListener('submit',function(e){
    e.preventDefault();
    const btn=document.getElementById('loginBtn');
    const txt=document.getElementById('btnTxt');
    txt.textContent='Entering hyperspace…';
    btn.disabled=true;
    warping=true;
    // Flash white warp overlay
    setTimeout(()=>{
        document.getElementById('warp-overlay').classList.add('active');
    }, 600);
    setTimeout(()=>this.submit(), 1400);
});
</script>
</body>
</html>
