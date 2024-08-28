import axios from 'axios';
import qs from 'qs';
export default  {
    async get(url, headers={}){
         headers.Authorization= `Bearer ${localStorage.getItem('admin_access_token')}`;
      const res =   await axios.get(url,  { headers:headers });
      return res.data;
    },
    async post(url, data, headers={}){
        headers.Authorization= `Bearer ${localStorage.getItem('admin_access_token')}`;
           
      const res =   await axios.post(url, data, { headers:headers });
      return res.data;
    },

    async getDataTableRecord(url, payload, columns) {
        
        const defaultQuery = {
            draw:0,
            columns:[],
            order:[],
            start:(payload.page - 1) * payload.itemsPerPage,
            length:payload.itemsPerPage,
            search:{
                value: payload.search,
                
            },
            ...payload.filters,

        }
        const i=0;
        for( let column of columns){
            defaultQuery.columns.push(
                {
                    data:column.key,
                    searchable:column.searchable == undefined ? true:column.searchable,
                    orderable:column.orderable == undefined ? true:column.orderable,
                    name:'',
                }
            )
        }
        for(let sort of payload.sortBy){
            const index= columns.findIndex(column=>column.key==sort.key)
            defaultQuery.order.push({
                column:index,
                dir:sort.order,
                name:'',

            })
        }
        const data = await this.post(url, qs.stringify(defaultQuery));
        return {
            items:data.data,
            total:data.recordsTotal,
        }
    },
    
}
