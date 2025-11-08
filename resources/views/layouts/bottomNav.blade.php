<!-- Bottom Navbar -->
<style>
    .appBottomMenu {
        display: flex;
        justify-content: space-around;
        align-items: center;
        height: 70px;
        background: #fff;
        box-shadow: 0 -1px 6px rgba(0,0,0,0.1);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 99;
    }
    .appBottomMenu .item {
        text-align: center;
        flex: 1;
        color: #444;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .appBottomMenu .item ion-icon {
        font-size: 22px;
    }
    .appBottomMenu .item div {
        font-size: 12px;
        margin-top: 2px;
    }
    .appBottomMenu .item.active {
        color: #9B7EBD;
    }
    .item-center-wrapper {
        margin-top: -30px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .item-center {
        background: #9B7EBD;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        color: #fff;
    }
    .item-center ion-icon {
        font-size: 26px;
        color: #fff;
    }
</style>

<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ request()->is('dashboard') ? 'active' : '' }}">
        <ion-icon name="home-outline"></ion-icon>
        <div>Home</div>
    </a>
    <a href="/presensi/histori" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
        <ion-icon name="timer-outline"></ion-icon>
        <div>Histori</div>
    </a>
    <a href="/presensi/create" class="item {{ request()->is('presensi/create') ? 'active' : '' }}">
        <div class="item-center-wrapper">
            <div class="item-center">
                <ion-icon name="camera"></ion-icon>
            </div>
            <br>
            {{-- <div>Presensi</div> --}}
        </div>
    </a>
    <a href="/presensi/izin" class="item {{ request()->is('presensi/izin') ? 'active' : '' }}">
        <ion-icon name="document-text-outline"></ion-icon>
        <div>Izin</div>
    </a>
    <a href="/editprofile" class="item {{ request()->is('editprofile') ? 'active' : '' }}">
        <ion-icon name="people-outline"></ion-icon>
        <div>Profile</div>
    </a>
</div>
<!-- * Bottom Navbar -->
